@extends('layouts.app')
@section('content')

<div class="app-title">
  <div>
    <h1><i class="fa fa-edit text-primary"></i> Editar usuario</h1>
    <p>Modificación de usuarios en el sistema</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="/admin/usuarios">Usuarios</a></li>
      <li class="breadcrumb-item"><a href="#">Editar usuario</a></li>
  </ul>
</div>
<div class="row">
  <div class="col-md-6 offset-md-3">
    <form method="post" action="/admin/usuarios/{{$user->id}}">
      <div class="tile">
        <h3 class="tile-title">Datos del usuario</h3>
        <div class="tile-body">
            @csrf
            @method('PUT')
            <input type="number" name="id" value="{{$user->id}}" hidden>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="control-label">Nombre</label>
                <input name="nombre" type="text" placeholder="Ingrese el o los nombres"
                class="form-control{{ $errors->has('nombre') ? ' is-invalid' : '' }}" 
                value="{{ (old('nombre')) ? old('nombre') : $user->nombre }}"
                required autofocus autocomplete>
                @if ($errors->has('nombre'))
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $errors->first('nombre') }}</strong>
                  </span>
                @endif
              </div> <!--form-group-->
              <div class="form-group col-md-6">
                <label class="control-label">Apellido</label>
                <input name="apellido" type="text" placeholder="Ingrese el o los apellidos"
                class="form-control{{ $errors->has('apellido') ? ' is-invalid' : '' }}" 
                value="{{ (old('apellido')) ? old('apellido') : $user->apellido}}"
                required autocomplete>
                @if ($errors->has('apellido'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('apellido') }}</strong>
                    </span>
                @endif
              </div> <!--form-group-->
            </div> <!--form-row-->
            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="control-label">Email</label>
                <input name="email" type="email" placeholder="Ingrese la dirección email"
                class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" 
                value="{{ (old('email')) ? old('email') : $user->email }}"
                required autocomplete>
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
              </div> <!--form-group-->
              <div class="form-group col-md-6">
                <label class="control-label">Teléfono</label>
                <input name="telefono" type="tel" placeholder="Ingrese un número de teléfono"
                class="form-control{{ $errors->has('telefono') ? ' is-invalid' : '' }}" 
                value="{{ (old('telefono')) ? old('telefono') : $user->telefono }}"
                required autocomplete>
                @if ($errors->has('telefono'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('telefono') }}</strong>
                    </span>
                @endif
              </div> <!--form-group--> 
            </div> <!--form-row-->
            <div class="form-group">
              <label for="rol">Rol</label>
              <select class="form-control" id="rol" name="rol">
                  <option value="Administrador" 
                  {{ (old("rol") ?: $user->role->nombre) == 'Administrador' ? "selected": "" }}>
                    Administrador
                  </option>
                  <option value="Recepcionista" 
                  {{ (old("rol") ?: $user->role->nombre) == 'Recepcionista' ? "selected" : "" }}>
                    Recepcionista
                   </option>
              </select>
            </div> <!--form-group--> 
        </div> <!-- tile-body -->
        <div class="tile-footer text-center">
          <button class="btn btn-primary" type="submit">
              <i class="fa fa-fw fa-lg fa-check-circle"></i>
              Guardar
          </button>&nbsp;&nbsp;&nbsp;
            <a class="btn btn-secondary" href="/admin/usuarios">
                <i class="fa fa-fw fa-lg fa-times-circle"></i>
                Volver
            </a>
        </div>
      </div> <!-- tile -->
    </form>
  </div> <!-- col -->
</div> <!-- row -->
@endsection
