<div class="control-group">
    <label for="NCMs" class="control-label">NCM<span class="required">*</span></label>
    <div class="controls">
        <div class="input-group" style="display: flex; gap: 5px;">
            <input id="NCMs" class="form-control" type="text" name="NCMs" value="<?php echo $result->NCMs; ?>" />
            <button type="button" class="btn btn-success" id="btnBuscarNcm" style="border-radius: 4px;"><i class="fas fa-search"></i></button>
            <button type="button" class="btn btn-warning" id="btnDescricaoNcm" style="border-radius: 4px;"><i class="fas fa-info-circle"></i></button>
        </div>
        <input id="ncm_id" class="form-control" type="hidden" name="ncm_id" value="<?php echo $result->ncm_id; ?>" />
    </div>
</div> 