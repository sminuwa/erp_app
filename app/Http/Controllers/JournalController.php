<?php

namespace App\Http\Controllers;

use App\Classes\Transaction;
use App\Models\Journal;
use App\Models\JournalItem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    //
    public function index()
    {
        \Cart::clear();
        $journals = Journal::where('branch_id', User::userBranchAction())->orderBy('status', 'ASC')->orderBy('date', 'DESC')->get();
        return view('pages.journals.index', compact('journals'));
    }

    public function store(Request $request)
    {
        try {

            $cart_items = \Cart::getContent();
            if ($cart_items->count() == 1) {
                session()->flash('app_error', 'You must add at least one journal to the cart!');
                return redirect()->back();
            }

            DB::beginTransaction();
            $journal = new Journal();
            $date = $request->date;
            $ym = Carbon::parse($date)->format('ym');
            $journal->reference = Journal::generateNewNumber('JNL', 4, $ym);
            $journal->description = $request->description;
            $journal->date = $date;
            $journal->branch_id = User::userBranchAction();
            $journal->created_by = auth()->id();
            $journal->status = 0;

            if ($journal->save()) {
                foreach ($cart_items as $item) {
                    $attr = $item->attributes;
                    $items[] = [
                        'journal_id' => $journal->id,
                        'branch_id' => $attr->branch_id ?? auth()->user()->branch->id,
                        'account_type' => $attr->account_type,
                        'account_id' => $attr->payer_id,
                        'credit' => str_replace(',', '', $attr->credit),
                        'debit' => str_replace(',', '', $attr->debit),
                        'description' => $attr->description ?? null,
                    ];
                }
            }
            if (JournalItem::upsert($items, ['journal_id', 'account_id', 'credit', 'debit'])) {
                DB::commit();

                \Cart::clear();
            } else {
                DB::rollback();
            }
            session()->flash('message', 'Journal created Successfully.');
            return redirect(route('journal.show', $journal->id));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
        return redirect(route('journal.index'));
    }

    public function create(Request $request)
    {
        //\Cart::clear();
        $type = 'journal';
        return view('pages.journals.create', compact('type'));
    }
    public function update(Request $request, Journal $journal)
    {
        try {
            $cart_items = \Cart::getContent();
            if ($cart_items->count() == 1) {
                session()->flash('app_error', 'You must add at least one journal to the cart!');
                return redirect()->back();
            }

            DB::beginTransaction();
            $date = $request->date;
            $journal->description = $request->description;
            $journal->date = $date;
            $journal->branch_id = User::userBranchAction();
            $journal->created_by = auth()->id();
            $journal->status = 0;
            $journal->items()->delete();
            $items = [];
            if ($journal->save()) {
                foreach ($cart_items as $item) {
                    $attr = $item->attributes;
                    $items[] = [
                        'journal_id' => $journal->id,
                        'branch_id' => $attr->branch_id ?? auth()->user()->branch->id,
                        'account_type' => $attr->account_type,
                        'account_id' => $attr->payer_id,
                        'credit' => str_replace(',', '', $attr->credit),
                        'debit' => str_replace(',', '', $attr->debit),
                        'description' => $attr->description ?? null,
                    ];
                }
            }
            if (JournalItem::upsert($items, ['journal_id', 'account_id', 'credit', 'debit'])) {
                DB::commit();
                \Cart::clear();
            } else {
                DB::rollback();
            }
            session()->flash('message', 'Journal updated Successfully.');
            return redirect(route('journal.show', $journal->id));
        } catch (\Exception $e) {
            session()->flash('app_error', $e->getMessage());
            return redirect()->back();
        }
        return redirect(route('journal.index'));
    }

    public function show(Journal $journal)
    {
        return view('pages.journals.show', compact('journal'));
    }

    public function post(Journal $journal)
    {
        if ($journal->status == 0) {
            DB::beginTransaction();
            $journal->status = 1;
            $journal->posted_by = auth()->id();
            $account_details = [];
            $debit = $credit = 0;
            foreach ($journal->items as $item) {
                $debit += intval($item->debit);
                $credit += intval($item->credit);
                $account_details[] = [
                    'account_id' => $item->account_id,
                    'branch_id' => $item->branch_id,
                    'account_type' => $item->account_type,
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                ];
            }
            
            //return Transaction::journal($account_details, $journal->reference, $journal->date);
            return round($debit,0).' - '.round($credit,0);
            if ((round($debit) != round($credit)))
                return back()->with('error', 'Cannot post journal. Make sure total debit and credit are equal.');
            //return Transaction::journal($account_details,$journal->reference,$journal->date);
            //return $debit - $credit;
            if ($journal->save()) {
                if (Transaction::journal($account_details, $journal->reference, $journal->date)) {
                    DB::commit();
                    return back()->with('success', 'posted successfully');
                } else
                    DB::rollback();
                return back()->with('error', 'Something went wrong.');
            }
        }
        return back();
    }

    public function reverse(Journal $journal)
    {
        try {
            DB::beginTransaction();
            $journal->status = 0;
            $journal->updated_by = auth()->id();
            if ($journal->save()) {
                if (Transaction::reversal($journal->reference)['status']) {
                    DB::commit();
                    return back()->with('success', 'Reversed successfully');
                } else
                    DB::rollback();
                return back()->with('error', 'Something went wrong.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }

    public function print(Journal $journal)
    {
        return view('pages.journals.print', ['journal' => $journal, 'setting' => Setting::first()]);
    }

    public function edit(Journal $journal)
    {
        // return $journal->items;
        foreach ($journal->items as $item) {
            \Cart::add([
                'id' => $item->id,
                'name' => $item->account()->name ?? $item->account()->description,
                'price' => str_replace(',', '', $item->credit) ?? 1,
                'quantity' => 1,
                'attributes' => array(
                    'credit' => str_replace(',', '', $item->credit) ?? 1,
                    'debit' => str_replace(',', '', $item->debit) ?? 1,
                    'description' => $item->description,
                    'code' => $item->account()->code ?? $item->account()->number,
                    'account_type' => $item->account_type,
                    'payer_id' => $item->account_id,
                    'branch_id' => $journal->branch_id,
                ),
            ]);
        }
        return view('pages.journals.edit', compact('journal'));
    }

    public function delete(Journal $journal)
    {
        if ($journal->delete()) {
            return redirect(route('journal.index'))->with('success', 'Reversed successfully');
        }
        return back()->with('error', 'Something went wrong.');
    }



}
