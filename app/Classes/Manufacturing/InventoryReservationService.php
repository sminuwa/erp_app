<?php

namespace App\Classes\Manufacturing;

use App\Models\InventoryReservation;
use App\Models\StoreProduct;

class InventoryReservationService
{
    /**
     * Reserve inventory for a reference
     *
     * @param string $reference_type
     * @param int $reference_id
     * @param int $product_id
     * @param int $store_id
     * @param float $qty
     * @return InventoryReservation|false
     */
    public static function reserve($reference_type, $reference_id, $product_id, $store_id, $qty)
    {
        // Check if there's enough available quantity
        $available = self::getAvailableQty($product_id, $store_id);

        if ($available < $qty) {
            return false;
        }

        return InventoryReservation::create([
            'reference_type' => $reference_type,
            'reference_id' => $reference_id,
            'product_id' => $product_id,
            'store_id' => $store_id,
            'reserved_qty' => $qty,
            'status' => InventoryReservation::STATUS_RESERVED
        ]);
    }

    /**
     * Reserve multiple items at once
     *
     * @param string $reference_type
     * @param int $reference_id
     * @param array $items Array of ['product_id' => ..., 'store_id' => ..., 'qty' => ...]
     * @return array ['status' => bool, 'message' => string, 'reservations' => array]
     */
    public static function reserveMultiple($reference_type, $reference_id, $items)
    {
        // First validate all items have sufficient quantity
        $validation = self::validateAvailability($items);
        if (!$validation['status']) {
            return $validation;
        }

        $reservations = [];

        foreach ($items as $item) {
            $reservation = self::reserve(
                $reference_type,
                $reference_id,
                $item['product_id'],
                $item['store_id'],
                $item['qty']
            );

            if (!$reservation) {
                // Rollback already created reservations
                self::releaseAllForReference($reference_type, $reference_id);
                return [
                    'status' => false,
                    'message' => "Failed to reserve product {$item['product_id']} in store {$item['store_id']}",
                    'reservations' => []
                ];
            }

            $reservations[] = $reservation;
        }

        return [
            'status' => true,
            'message' => 'All items reserved successfully',
            'reservations' => $reservations
        ];
    }

    /**
     * Release all reservations for a reference
     *
     * @param string $reference_type
     * @param int $reference_id
     * @return int Number of updated records
     */
    public static function releaseAllForReference($reference_type, $reference_id)
    {
        return InventoryReservation::releaseAllForReference($reference_type, $reference_id);
    }

    /**
     * Consume all reservations for a reference (mark as used)
     *
     * @param string $reference_type
     * @param int $reference_id
     * @return int Number of updated records
     */
    public static function consumeAllForReference($reference_type, $reference_id)
    {
        return InventoryReservation::consumeAllForReference($reference_type, $reference_id);
    }

    /**
     * Get available quantity for a product in a store (excluding reserved)
     *
     * @param int $product_id
     * @param int $store_id
     * @return float
     */
    public static function getAvailableQty($product_id, $store_id)
    {
        return InventoryReservation::getAvailableQty($product_id, $store_id);
    }

    /**
     * Get total reserved quantity for a product in a store
     *
     * @param int $product_id
     * @param int $store_id
     * @return float
     */
    public static function getTotalReservedQty($product_id, $store_id)
    {
        return InventoryReservation::getTotalReservedQty($product_id, $store_id);
    }

    /**
     * Validate availability for multiple items
     *
     * @param array $items Array of ['product_id' => ..., 'store_id' => ..., 'qty' => ...]
     * @return array ['status' => bool, 'message' => string, 'insufficient' => array]
     */
    public static function validateAvailability($items)
    {
        $insufficient = [];

        foreach ($items as $item) {
            $available = self::getAvailableQty($item['product_id'], $item['store_id']);

            if ($available < $item['qty']) {
                $insufficient[] = [
                    'product_id' => $item['product_id'],
                    'store_id' => $item['store_id'],
                    'required_qty' => $item['qty'],
                    'available_qty' => $available,
                    'shortage' => $item['qty'] - $available
                ];
            }
        }

        if (count($insufficient) > 0) {
            return [
                'status' => false,
                'message' => 'Insufficient inventory for ' . count($insufficient) . ' item(s)',
                'insufficient' => $insufficient
            ];
        }

        return [
            'status' => true,
            'message' => 'All items have sufficient inventory',
            'insufficient' => []
        ];
    }

    /**
     * Check if a specific quantity can be reserved
     *
     * @param int $product_id
     * @param int $store_id
     * @param float $qty
     * @return bool
     */
    public static function canReserve($product_id, $store_id, $qty)
    {
        return self::getAvailableQty($product_id, $store_id) >= $qty;
    }

    /**
     * Get all active reservations for a reference
     *
     * @param string $reference_type
     * @param int $reference_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getReservationsForReference($reference_type, $reference_id)
    {
        return InventoryReservation::forReference($reference_type, $reference_id)
            ->reserved()
            ->get();
    }

    /**
     * Update reservation quantity
     *
     * @param int $reservation_id
     * @param float $new_qty
     * @return bool
     */
    public static function updateReservationQty($reservation_id, $new_qty)
    {
        $reservation = InventoryReservation::find($reservation_id);

        if (!$reservation || !$reservation->isReserved()) {
            return false;
        }

        // Check if there's enough available for the increase
        if ($new_qty > $reservation->reserved_qty) {
            $increase = $new_qty - $reservation->reserved_qty;
            $available = self::getAvailableQty($reservation->product_id, $reservation->store_id);

            if ($available < $increase) {
                return false;
            }
        }

        $reservation->reserved_qty = $new_qty;
        return $reservation->save();
    }
}
