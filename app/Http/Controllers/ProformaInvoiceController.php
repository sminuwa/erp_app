<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Proformer;
use Illuminate\Http\Request;

class ProformaInvoiceController extends Controller
{
    //

    public function delete(Proformer $proforma){
        if($proforma->delete()){
            $action = "Deleted proforma invoice with reference " . $proforma->reference;
            AuditLog::auditLog(auth()->id(), $action);
            session()->flash('app_message', 'Deleted successfully');
        }
        return redirect()->back();
    }
}
