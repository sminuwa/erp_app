<div>
   <div class="card">
       <div class="card-body">
           <div class="col-12">
               @if(session()->has('message'))
                   <div class="alert alert-success">{{ session('message') }}</div>
               @endif
                   @if(session()->has('error'))
                       <div class="alert alert-success">{{ session('error') }}</div>
                   @endif
           </div>
           <div class="col-12 mb-3">
               <h5>Journal Details</h5>
           </div>
           <form wire:submit.prevent="store">
               <div class="row">
                   <div class="col-md-12" style="border-right:#dddddd solid">

                   </div>
                   <div class="col-md-5 mb-3">
                       <input wire:model="journal_date" type="date" class="form-control " required>
                       <label class="floating-label">Date: @error('journal_date')<span class="text-danger error">{{ $message }}</span>@enderror</label>
                   </div>
                   <div class="col-md-7 mb-3">
                       <input wire:model="description" type="text" class="form-control" placeholder="">
                       <label class="floating-label">Description: @error('description')<span class="text-danger error">{{ $message }}</span>@enderror</label>
                   </div>

                   <div class="col-md-12">
                       <div class="row">
                           <div class="col-md-12 mb-3">
                               <button class="btn text-white btn-info btn-sm" wire:click.prevent="add({{$i}})">Add</button>
                           </div>
                           <div class="col-md-12">
                               <table>
                               @foreach($inputs as $key => $value)
                                   <tr>
                                       <td>
                                           <div class="col-12">
                                               <div class="form-group">
                                                   <select  wire:model="type.{{ $value }}" wire:change="changeTypeEvent($event.target.value)" class="form-control select2-single" required>
                                                       <option value="">Select...</option>
                                                       <option value="Customer">Customer</option>
                                                       <option value="Supplier">Supplier
                                                       <option value="GeneralAccount">General Accounts
                                                       </option>
                                                   </select>
                                                   <label class="floating-label">Account Type: @error('type.'.$value)<span class="text-danger error">{{ $message }}</span>@enderror</label>
                                               </div>
                                           </div>
                                       </td>
                                       <td>
                                           <div class="col-12">
                                               <div class="form-group">
                                                   <select wire:model="account.{{ $value }}" class="form-control select2-single" required>
                                                       <option value="">Select...</option>
                                                        @if(isset($accounts[$value]))
                                                           @foreach($accounts[$value] as $account)
                                                               <option value="{{ $account['id'] }}"> {{ $account['number'] ?? ($account['code'] ?? "")  }} - {{ $account['description'] ?? ($account['name'] ?? "") }}</option>
                                                           @endforeach
                                                        @endif
                                                   </select>
                                                   <label class="floating-label">Account: @error('account.'.$value) <span class="text-danger error">{{ $message }}</span>@enderror</label>
                                               </div>
                                           </div>
                                       </td>
                                       <td>
                                           <div class="col-12">
                                               <div class="form-group">
                                                   <input type="number" min="0" class="form-control" placeholder="Debit" wire:model="debit.{{ $value }}" wire:change="totals">
                                                   <label class="floating-label">Debit: @error('debit.'.$value) <br><span class="text-danger error">{{ $message }}</span>@enderror</label>
                                               </div>
                                           </div>
                                       </td>
                                       <td>
                                           <div class="col-12">
                                               <div class="form-group">
                                                   <input type="number" min="0" class="form-control" placeholder="Credit" wire:model="credit.{{ $value }}" wire:change="totals">
                                                   <label class="floating-label">Credit: @error('credit.'.$value) <br><span class="text-danger error">{{ $message }}</span>@enderror</label>
                                               </div>
                                           </div>
                                       </td>
                                       <td>
                                           <div class="col-12">
                                               <div class="input-group mb-3">
                                                   <input type="text" class="form-control" placeholder="" wire:model="desc.{{ $value }}" >
                                                   <label class="floating-label">Description: @error('desc.'.$value) <br><span class="text-danger error">{{ $message }}</span>@enderror</label>
                                                   <div class="input-group-append">
                                                       <button class="btn btn-danger btn-sm" wire:click.prevent="remove({{$key}})"><i class="fa fa-trash"></i></button>
                                                   </div>
                                               </div>
                                           </div>
                                       </td>
                                   </tr>
                               @endforeach
                               </table>
                           </div>
                           <div class="col-md-12 mt-3">
                               <h4>Total Credit: #{{ $total_credit }} <br>Total Debit: #{{ $total_debit }} <br>Balance: #{{ $total_credit-$total_debit }}</h4>
                           </div>
                           <div class="col-md-12 mt-3">
                               <button type="submit"  class="btn btn-success btn-sm">Save</button>
                           </div>
                       </div>
                   </div>
               </div>
           </form>
       </div>
   </div>
</div>
<script>
    $('.select2-single').on('change', function (e) {
        var data = $('.select2-single').select2("val");
        @this.set('selected', data);
    });
    $('.datepicker').on('change', function (e) {
        @this.set('datepicker', e.target.value);
    });
</script>
