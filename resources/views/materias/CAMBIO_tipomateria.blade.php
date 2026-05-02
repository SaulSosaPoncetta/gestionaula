{{--
    REEMPLAZAR el bloque de "tipo de materia" en materias/create.blade.php y materias/edit.blade.php
    por este bloque. También eliminar el campo "cantidadhoras".

    BLOQUE A REEMPLAZAR (buscar en la vista):
        <label ... >Tipo de materia</label>
        ... enum tipomateria con general/tecnicoespecifica/cientificotecnologica ...

    REEMPLAZAR CON:
--}}

<div class="col-md-3">
    <label class="form-label fw-semibold">Tipo de espacio</label>
    <select name="tipomateria" class="form-select">
        <option value="">—</option>
        <option value="aula"   {{ old('tipomateria', $materia->tipomateria ?? '') === 'aula'   ? 'selected' : '' }}>Aula</option>
        <option value="taller" {{ old('tipomateria', $materia->tipomateria ?? '') === 'taller' ? 'selected' : '' }}>Taller</option>
    </select>
</div>

{{--
    ELIMINAR este bloque completo (ya no existe el campo cantidadhoras):

    <div class="col-md-2">
        <label class="form-label fw-semibold">Cant. horas</label>
        <input type="number" name="cantidadhoras" ...>
    </div>
--}}
