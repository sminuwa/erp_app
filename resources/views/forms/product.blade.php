<form action="{{ isset($route) ? $route : route('products.store') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />
    <div class="form-group">
        <label for="company_id">Company</label>
        <select type="number"
            class="form-control ajax-companies select2-single {{ $errors->has('company_id') ? ' is-invalid' : '' }}"
            name="company_id" id="company_id" selected_item="{{ $model->company_id }}" required="required">
        </select>
        @if ($errors->has('company_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('company_id') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="category_id">Category</label>
        <select type="number"
            class="form-control ajax-categories select2-single {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
            name="category_id" id="category_id" selected_item="{{ $model->category_id }}" required="required">
        </select>
        @if ($errors->has('category_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('category_id') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="name">Product Name</label>
        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
            id="name" value="{{ old('name', $model->name) }}" placeholder="" maxlength="191" required="required">
        @if ($errors->has('name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="name">Product Information</label>
        <input type="text" class="form-control {{ $errors->has('information') ? ' is-invalid' : '' }}" name="information"
               id="name" value="{{ old('information', $model->name) }}" placeholder="" maxlength="191"">
        @if ($errors->has('information'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('information') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="unit">Base UTM</label>
        <select class="form-control select2-single {{ $errors->has('unit') ? ' is-invalid' : '' }}"
            name="unit" id="unit" selected_item="{{ $model->unit }}" required="required">
            <option value="">Select...</option>
            <option value="BD" {{ $model->unit == 'DB' ? 'selected' : '' }}>BD</option>
            <option value="BG" {{ $model->unit == 'BG' ? 'selected' : '' }}>BG</option>
            <option value="Buk" {{ $model->unit == 'Buk' ? 'selected' : '' }}>Buk</option>
            <option value="COL" {{ $model->unit == 'COL' ? 'selected' : '' }}>COL</option>
            <option value="CS" {{ $model->unit == 'CS' ? 'selected' : '' }}>CS</option>
            <option value="CTN" {{ $model->unit == 'CTN' ? 'selected' : '' }}>CTN</option>
            <option value="DR" {{ $model->unit == 'DR' ? 'selected' : '' }}>DR</option>
            <option value="GAL" {{ $model->unit == 'GAL' ? 'selected' : '' }}>GAL</option>
            <option value="JAR" {{ $model->unit == 'JAR' ? 'selected' : '' }}>JAR</option>
            <option value="K" {{ $model->unit == 'K' ? 'selected' : '' }}>K</option>
            <option value="KG" {{ $model->unit == 'KG' ? 'selected' : '' }}>KG</option>
            <option value="KIT" {{ $model->unit == 'KIT' ? 'selected' : '' }}>KIT</option>
            <option value="L" {{ $model->unit == 'L' ? 'selected' : '' }}>L</option>
            <option value="LB" {{ $model->unit == 'LB' ? 'selected' : '' }}>LB</option>
            <option value="LBS" {{ $model->unit == 'LBS' ? 'selected' : '' }}>LBS</option>
            <option value="PTR" {{ $model->unit == 'PTR' ? 'selected' : '' }}>PTR</option>
            <option value="PC" {{ $model->unit == 'PC' ? 'selected' : '' }}>PC</option>
            <option value="PCS" {{ $model->unit == 'PCS' ? 'selected' : '' }}>PCS</option>
            <option value="PSC" {{ $model->unit == 'PSC' ? 'selected' : '' }}>PSC</option>
            <option value="Rol" {{ $model->unit == 'Rol' ? 'selected' : '' }}>Rol</option>
            <option value="SAC" {{ $model->unit == 'SAC' ? 'selected' : '' }}>SAC</option>
            <option value="Set" {{ $model->unit == 'Set' ? 'selected' : '' }}>Set</option>
            <option value="SHT" {{ $model->unit == 'SHT' ? 'selected' : '' }}>SHT</option>
            <option value="SQM" {{ $model->unit == 'SQM' ? 'selected' : '' }}>SQM</option>
            <option value="ST" {{ $model->unit == 'ST' ? 'selected' : '' }}>ST</option>
            <option value="T" {{ $model->unit == 'T' ? 'selected' : '' }}>T</option>
            <option value="TN" {{ $model->unit == 'TN' ? 'selected' : '' }}>TN</option>
            <option value="TRK" {{ $model->unit == 'TRK' ? 'selected' : '' }}>TRK</option>
            <option value="TRP" {{ $model->unit == 'TRP' ? 'selected' : '' }}>TRP</option>
            <option value="Yrd" {{ $model->unit == 'Yrd' ? 'selected' : '' }}>Yrd</option>
        </select>
        @if ($errors->has('unit'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('unit') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="barcode">Barcode</label>
        <input type="text" class="form-control {{ $errors->has('barcode') ? ' is-invalid' : '' }}" name="barcode"
            id="barcode" value="{{ old('barcode', $model->barcode) }}" placeholder="">
        @if ($errors->has('barcode'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('barcode') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="barcode">This product can expire</label>
        <div class="form-check">
            <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio"
                value="1" name="expiry_status" id="status_yes"
                {{ $model->expiry_status == null || $model->expiry_status == 1 ? 'checked' : '' }}>
            Yes
            &nbsp;&nbsp;
            &nbsp;&nbsp;
            <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio"
                value="0" checked name="expiry_status" id="status_no"
                {{ $model->expiry_status != null && $model->expiry_status == 0 ? 'checked' : '' }}> No

            @if ($errors->has('status'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('status') }}</strong>
                </div>
            @endif
        </div>
    </div>
    <div class="form-group">
        <label for="barcode">Status</label>
        <div class="form-check">
            <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio"
                value="1" name="status" id="status_yes"
                {{ $model->status == null || $model->status == 1 ? 'checked' : '' }}>
            Active
            &nbsp;&nbsp;
            &nbsp;&nbsp;
            <input class="form-check-input {{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio"
                value="0" name="status" id="status_no"
                {{ $model->status != null && $model->status == 0 ? 'checked' : '' }}> Not
            Active
            @if ($errors->has('status'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('status') }}</strong>
                </div>
            @endif
        </div>
    </div>

    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Save" />

    </div>
</form>
