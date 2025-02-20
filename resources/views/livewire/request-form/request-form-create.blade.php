<div>
    <div class="card">
        <div class="card-header">
            </i> Formulario de Requerimiento</h6>
        </div>
        <div class="card-body">
            <div class="form-row">
                <fieldset class="form-group col-sm-4">
                    <label for="forRut">Nombre de Formulario:</label>
                    <input wire:model.defer="name" name="name" class="form-control form-control-sm" type="text" placeholder="EJ: ADIQUISICIÓN DE MOBILIARIO PARA OFICINA..." value="">
                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                </fieldset>

                {{--<fieldset class="form-group col-sm-4">
                    <label>Administrador de Contrato:</label><br>
                    <div wire:ignore id="for-bootstrap-select">
                      <select wire:model.defer="contractManagerId" name="contractManagerId" data-container="#for-bootstrap-select"
                        class="form-control form-control-sm selectpicker show-tick" data-live-search="true" data-size="5" {{ $editRF ? 'disabled' : 'required' }} >
                        <option>Seleccione...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ ucfirst(trans($user->FullName)) }}</option>
                        @endforeach
                      </select>
                    </div>
                    @error('contractManagerId') <span class="text-danger small">{{ $message }}</span> @enderror
                </fieldset>--}}
                
                <fieldset class="form-group col-12 col-md-4">
                    <label for="for_requester_id">Administrador de Contrato:</label>
                    @livewire('search-select-user', [
                        'emit_name' => 'searchedContractManager',
                        'small_option' => true,
                        'user' => $contractManager,
                    ])
                </fieldset>

                
                {{-- <fieldset class="form-group col-sm-{{$program_id == 'other' ? 1 : 4}}"> --}}
                <fieldset class="form-group col-sm">
                    {{-- <label>Programa @if($program_id != 'other')Asociado:@endif</label><br> --}}
                    <label>Programa Asociado</label><br>
                    <select wire:model="program_id" name="program_id" class="form-control form-control-sm " required>
                        <option value="">Seleccione...</option>
                        @foreach($lstProgram as $val)
                        <option value="{{$val->id}}">{{$val->alias_finance}} {{$val->period}} - Subtítulo {{$val->Subtitle->name}}</option>
                        @endforeach
                        {{-- <option value="other">Otro</option> --}}
                    </select>
                    @error('program_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </fieldset>
                {{--
                @if($program_id == 'other')
                <fieldset class="form-group col-sm-3">
                    <label for="forRut">&nbsp;</label>
                    <input wire:model.defer="program" name="program" class="form-control form-control-sm" type="text" placeholder="Nombre del programa asociado">
                    @error('program') <span class="text-danger small">{{ $message }}</span> @enderror
                </fieldset>
                @endif
                --}}
            </div>

            <div class="form-row">
                <fieldset class="form-group col-sm-4">
                    <label>Mecanismo de Compra:</label><br>
                    <select wire:model="purchaseMechanism" name="purchaseMechanism" class="form-control form-control-sm " required>
                        <option value="">Seleccione...</option>
                        @foreach($lstPurchaseMechanism as $val)
                            <option value="{{$val->id}}">{{$val->name}}</option>
                        @endforeach
                    </select>
                    @error('purchaseMechanism') <span class="text-danger small">{{ $message }}</span> @enderror
                </fieldset>

                <fieldset class="form-group col-sm-4">
                    <label>Tipo:</label><br>
                    <select wire:model.defer="subtype" name="subtype" class="form-control form-control-sm" required>
                        <option value="">Seleccione...</option>
                        <option value="bienes ejecución inmediata">Bienes Ejecución Inmediata</option>
                        <option value="bienes ejecución tiempo">Bienes Ejecución En Tiempo</option>
                        <option value="servicios ejecución inmediata">Servicios Ejecución Inmediata</option>
                        <option value="servicios ejecución tiempo">Servicios Ejecución En Tiempo</option>
                    </select>
                    @error('subtype') <span class="text-danger small">{{ $message }}</span> @enderror
                </fieldset>

                <fieldset class="form-group col-sm-4">
                    <label for="for_type_of_currency">Tipo de Moneda:</label>
                    <select wire:model.defer="typeOfCurrency" name="typeOfCurrency" class="form-control form-control-sm" required>
                        <option value="">Seleccione...</option>
                        <option value="peso">Peso</option>
                        <option value="dolar">Dolar</option>
                        <option value="uf">U.F.</option>
                    </select>
                    @error('typeOfCurrency') <span class="text-danger small">{{ $message }}</span> @enderror
                </fieldset>
            </div>

            <div class="form-row">
                <fieldset class="form-group col-sm-4">
                    <label for="for_calidad_juridica">Autoriza Jefatura Superior</label>
                    <div class="mt-1 ml-4">
                        <input class="form-check-input" type="checkbox" value="1" wire:model="superiorChief" name="superiorChief" @if((optional($requestForm)->status != 'saved' && $editRF) || $isHAH) disabled @endif >
                        <label class="form-check-label" for="flexCheckDefault">
                            Sí
                        </label>
                    </div>
                </fieldset>

                <fieldset class="form-group col-sm-4">
                    <label for="for_fileRequests" class="form-label">Documento(s) de Respaldo:</label>
                    <input class="form-control form-control-sm" wire:model.defer="fileRequests" id="for_fileRequests" type="file" style="padding:2px 0px 0px 2px;" name="fileRequests[]" multiple>
                    <div wire:loading wire:target="fileRequests">Cargando archivo(s)...</div>
                </fieldset>

                <fieldset class="form-group col-12 col-md-4">
                    <label for="for_technical_review_ou_id">
                        U.O. Evaluación Técnica (opcional)
                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip" 
                            title="Completar este campo en caso de requerir autorización de unidad organizacional competente">
                            <span class="fas fa-info-circle"></span>
                        </span>
                    </label>
                    @livewire('search-select-organizational-unit', [
                        'emit_name'            => 'searchedTechnicalReviewOu',
                        'selected_id'          => 'technical_review_ou_id',
                        'small_option'         => true
                    ])
                </fieldset>
            </div>

            <div class="form-row">
                <fieldset class="form-group col-sm">
                    <label for="exampleFormControlTextarea1" class="form-label">Justificación de Adquisición:</label>
                    <textarea wire:model.defer="justify" name="justify" class="form-control form-control-sm" rows="3"></textarea>
                    @error('justify') <span class="text-danger small">{{ $message }}</span> @enderror
                </fieldset>
            </div>
            <div class="form-row">
                <fieldset class="form-group col-sm">
                  @if (count($messagePM) > 0)
                      <label>Documentos que debe adjuntar:</label>
                      <div class="alert alert-warning mx-0 my-0 pt-2 pb-0 px-0" role="alert">
                        <ul>
                          @foreach ($messagePM as $val)
                            <li>{{ $val }}</li>
                          @endforeach
                        </ul>
                      </div>
                  @endif
                </fieldset>
            </div>
            @if($savedFiles && !$savedFiles->isEmpty())
            <div class="form-row">
                <fieldset class="form-group col-sm">
                      <label>Documentos adjuntos:</label>

                        <ul class="list-group">
                          @foreach ($savedFiles as $file)
                            <li class="list-group-item py-2">
                                {{ $file->name }}
                                <a onclick="return confirm('¿Está seguro de eliminar archivo con nombre {{$file->name}}?') || event.stopImmediatePropagation()" wire:click="destroyFile({{$file->id}})"
                                    class="btn btn-link btn-sm float-right" title="Eliminar"><i class="far fa-trash-alt" style="color:red"></i></a>
                                <a href="{{ route('request_forms.show_file', $file->id) }}"
                                    class="btn btn-link btn-sm float-right" title="Ver" target="_blank"><i class="far fa-eye"></i></a>
                            </li>
                          @endforeach
                        </ul>

                </fieldset>
            </div>
            @endif
        </div>
    </div>

    <br>
    @if($isRFItems)
        @livewire('request-form.item.request-form-items', ['savedItems' => $requestForm->itemRequestForms ?? null, 'savedTypeOfCurrency' => $requestForm->type_of_currency ?? null])
    @else
        @livewire('request-form.passenger.passenger-request', ['savedPassengers' => $requestForm->passengers ?? null, 'savedTypeOfCurrency' => $requestForm->type_of_currency ?? null])
    @endif

    <!-- <div class="row justify-content-md-end mt-0">
        <div class="col-2">
            <button wire:click="saveRequestForm"  class="btn btn-primary btn-sm float-right " type="button" wire:loading.attr="disabled">
                <i class="fas fa-save"></i> Guardar
            </button>
        </div>

        <div class="col-1">
            <button wire:click="saveRequestForm"  class="btn btn-primary btn-sm float-right " type="button" wire:loading.attr="disabled">
                Enviar
            </button>
        </div>
    </div> -->


    <div class="float-right">
        <button wire:click="saveRequestForm('save')"  class="btn btn-primary btn-sm" type="button" wire:loading.attr="disabled" @if($requestForm && $requestForm->hasFirstEventRequestFormSigned()) onclick="confirm('Estimado/a, está solicitando editar un formulario que está en proceso de firma vigente el cual requiere partir de cero o bien ya fue aprobado pero requiere volver a refrendación para su aprobación. ¿Ud. está de acuerdo con continuar?') || event.stopImmediatePropagation()" @endif>
            <i class="fas fa-save"></i> Guardar
        </button>

        <button wire:click="saveRequestForm('sent')"  class="btn btn-primary btn-sm" type="button" wire:loading.attr="disabled" @if($requestForm && $requestForm->hasEventRequestForms()) disabled @endif>
            <i class="fas fa-paper-plane"></i> Guardar y Enviar
        </button>

    </div>

    @if(count($errors) > 0)
    {{-- > 0 and ($errors->has('purchaseMechanism') or $errors->has('program') or $errors->has('justify') or $errors->has('items') or $errors->has('balance')))--}}

         <div class="alert alert-danger mt-5">
          <p>Corrige los siguientes errores:</p>
             <ul>
                 @foreach ($errors->all() as $message)
                     <li>{{ $message }}</li>
                 @endforeach
             </ul>
         </div>

    @endif

    <br>
</div>
