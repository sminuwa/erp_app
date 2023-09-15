<form action="{{isset($route)?$route:route('audit_logs.store')}}" method="POST" >
    {{csrf_field()}}
    <input type="hidden" name="_method" value="{{isset($method)?$method:'POST'}}"/>
        <div class="form-group">
        <label for="user_id">User Id</label>
        <input type="number" class="form-control {{ $errors->has('user_id') ? ' is-invalid' : '' }}" name="user_id" id="user_id" value="{{old('user_id',$model->user_id)}}" placeholder="" required="required" >
          @if($errors->has('user_id'))
    <div class="invalid-feedback">
        <strong>{{ $errors->first('user_id') }}</strong>
    </div>
  @endif 
    </div>

    <div class="form-group">
        <label for="action">Action</label>
        <input type="text" class="form-control {{ $errors->has('action') ? ' is-invalid' : '' }}" name="action" id="action" value="{{old('action',$model->action)}}" placeholder="" maxlength="200" required="required" >
          @if($errors->has('action'))
    <div class="invalid-feedback">
        <strong>{{ $errors->first('action') }}</strong>
    </div>
  @endif 
    </div>

    <div class="form-group">
        <label for="role_id">Role Id</label>
        <input type="number" class="form-control {{ $errors->has('role_id') ? ' is-invalid' : '' }}" name="role_id" id="role_id" value="{{old('role_id',$model->role_id)}}" placeholder="" required="required" >
          @if($errors->has('role_id'))
    <div class="invalid-feedback">
        <strong>{{ $errors->first('role_id') }}</strong>
    </div>
  @endif 
    </div>


    <div class="form-group text-right ">
        <input type="reset" class="btn btn-default" value="Clear"/>
        <input type="submit" class="btn btn-primary" value="Save"/>

    </div>
</form>