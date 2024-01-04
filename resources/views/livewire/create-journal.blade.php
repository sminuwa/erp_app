<div>
   <div class="card" >
       <div class="card-body">
           <div class="col-12">
               @if(session()->has('message'))
                   <div class="alert alert-success">{{ session('message') }}</div>
               @endif
                   @if(session()->has('error'))
                       <div class="alert alert-danger">{{ session('error') }}</div>
                   @endif
           </div>
           <div class="col-12 mb-3">
               <h5>Journal Details</h5>
           </div>

           <form wire:submit.prevent="store" wire:ignore.self>
               <div class="row">
                   <div class="col-md-12" style="border-right:#dddddd solid">

                   </div>
                   <div class="col-md-5 mb-3" wire:ignore>
                       <input wire:model="journal_date" type="date" class="form-control " required>
                       <label class="floating-label">Date: @error('journal_date')<span class="text-danger error">{{ $message }}</span>@enderror</label>
                   </div>
                   <div class="col-md-7 mb-3" wire:ignore>
                       <input wire:model="description" id="description" type="text" class="form-control" placeholder="Description">
                       <label class="floating-label">Description: @error('description')<span class="text-danger error">{{ $message }}</span>@enderror</label>
                   </div>

                   <div class="col-md-12">
                       <div class="row">
                           <div class="col-md-12 mb-3" wire:ignore>
                               <button class="btn text-white btn-info btn-sm" wire:click.prevent="add({{$i}})">Add</button>
                           </div>
{{--                           @json($accounts)--}}
                           <div class="col-md-12">
                               <table>
                               @foreach($inputs as $key => $value)
                                   <tr :wire:key="{{ $loop->index }}" wire:ignore.self>
                                       <td>
                                           <div class="col-12">
                                               <div class="form-group" wire:ignore>
                                                   <select wire:model="type.{{ $key }}" wire:change.lazy="changeTypeEvent($event.target.value, {{ $key }})" class="form-control  type select2-single" required>
                                                       <option value="">Select...</option>
                                                       <option value="Customer">Customer</option>
                                                       <option value="Supplier">Supplier
                                                       <option value="GeneralAccount">General Accounts
                                                       </option>
                                                   </select>
                                                   <label class="floating-label">Account Type: @error('type.'.$key)<span class="text-danger error">{{ $message }}</span>@enderror</label>
                                               </div>
                                           </div>
                                       </td>
                                       <td>
                                           <div class="col-12">
                                               <div class="form-group">
                                                   <select wire:ignore.self wire:model.lazy="account.{{ $key }}" class="form-control select2" required>
                                                       <option value="">Select...</option>
                                                        @if(isset($accounts[$key]))
                                                           @foreach($accounts[$key] as $account)
                                                               <option value="{{ $account['id'] }}"> {{ $account['number'] ?? ($account['code'] ?? "")  }} - {{ $account['description'] ?? ($account['name'] ?? "") }}</option>
                                                           @endforeach
                                                        @endif
                                                   </select>
                                                   <label class="floating-label">Account: @error('account.'.$key) <span class="text-danger error">{{ $message }}</span>@enderror</label>
                                               </div>
                                           </div>
                                       </td>
                                       <td>
                                           <div class="col-12">
                                               <div class="form-group" wire:ignore>
                                                   <input type="number" min="0" step=".01" class="form-control" placeholder="Debit" wire:model.lazy="debit.{{ $key }}" wire:change="totals">
                                                   <label class="floating-label">Debit: @error('debit.'.$key) <br><span class="text-danger error">{{ $message }}</span>@enderror</label>
                                               </div>
                                           </div>
                                       </td>
                                       <td>
                                           <div class="col-12">
                                               <div class="form-group" wire:ignore>
                                                   <input type="number" min="0" step=".01" class="form-control" placeholder="Credit" wire:model.lazy="credit.{{ $key }}" wire:change="totals">
                                                   <label class="floating-label">Credit: @error('credit.'.$key) <br><span class="text-danger error">{{ $message }}</span>@enderror</label>
                                               </div>
                                           </div>
                                       </td>
                                       <td>
                                           <div class="col-12">
                                               <div class="input-group mb-3" wire:ignore>
                                                   <input type="text" class="form-control" placeholder="Description" wire:model.lazy="desc.{{ $key }}" >
                                                   <label class="floating-label">Description: @error('desc.'.$key) <br><span class="text-danger error">{{ $message }}</span>@enderror</label>
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
                               <h4>
                                   <small>Total Credit:</small> N{{ $total_credit }} <br>
                                   <small>Total Debit:</small> N{{ $total_debit }} <br>
                                   <small>Balance:</small> N{{ $total_credit-$total_debit }}
                               </h4>
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

    <script>

    </script>
{{--    @push('scripts')--}}
        <script>
            /*document.addEventListener('livewire:load', function () {
                function initSelect2(){
                    $(".select2").select2();
                }
                // initSelect2()
                console.log('Navigated')
                $('body').on('change', '.type', function () {
                    initSelect2()
                    // let data = $(this).select2('val')
                    // console.log(data)
                })
               /!* $('body').on('change', @this.type, function () {
                    // initSelect2()
                    let data = $(this).select2('val')
                    console.log(data)
                })*!/

            })*/
        </script>
{{--    @endpush--}}
</div>

