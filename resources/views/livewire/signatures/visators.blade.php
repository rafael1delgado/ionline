<div>
    <div class="form-row">
        <fieldset class="form-group col-4">
            <label for="for_endorse_type">Tipo de visación</label>
            <select class="form-control" name="endorse_type" required="">
                <option value="No requiere visación">No requiere visación</option>
                <option value="Visación opcional">Visación opcional</option>
                <option value="Visación en cadena de responsabilidad">Visación en cadena de responsabilidad</option>
            </select>
        </fieldset>

        <fieldset class="form-group col-2">
            <label for="">&nbsp;</label>
            <button class="btn text-white btn-info btn-block" wire:click.prevent="add({{$i}})">Agregar</button>
        </fieldset>
    </div>
    <div class="form-row">
        <div class="col-5">
            <label for="">Unidad Organizacional</label>
        </div>
        <div class="col-5">
            <label for="">Visador</label>
        </div>
    </div>
    @foreach($inputs as $key => $value)
        <div class="form-row">
            <fieldset class="form-group col-5">
                <select name="ou_id_visator[]" wire:model="organizationalUnit.{{ $value }}" class="form-control">
                    <option value=''></option>

                    @foreach($ouRoots as $ouRoot)
                        <option value="{{ $ouRoot->id }}">
                            {{ $ouRoot->name }}
                        </option>
                        @foreach($ouRoot->childs as $child_level_1)
                            <option value="{{ $child_level_1->id }}">
                                &nbsp;&nbsp;&nbsp;
                                {{ $child_level_1->name }}
                            </option>
                            @foreach($child_level_1->childs as $child_level_2)
                                <option value="{{ $child_level_2->id }}">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ $child_level_2->name }}
                                </option>
                                @foreach($child_level_2->childs as $child_level_3)
                                    <option value="{{ $child_level_3->id }}">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        {{ $child_level_3->name }}
                                    </option>
                                    @foreach($child_level_3->childs as $child_level_4)
                                        <option value="{{ $child_level_4->id }}">
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            {{ $child_level_4->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach

                </select>
            </fieldset>
            <fieldset class="form-group col-5">
                @if(array_key_exists($value,$users))
                    <select name="user_visator[]" wire:model="user.{{$value}}" class="form-control">
                        <option value=''></option>
                        @foreach($users[$value] as $user)
                            <option value={{ $user->id }}>{{ $user->fullName }}</option>
                        @endforeach
                    </select>
                @endif
            </fieldset>
            <fieldset class="form-group col-md-2">
                <button class="btn btn-danger btn-block" wire:click.prevent="remove({{$key}})">Remover</button>
            </fieldset>
        </div>
    @endforeach
</div>
