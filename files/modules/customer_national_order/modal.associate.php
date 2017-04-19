<!-- Help Modal Trigger -->
<!-- //// HELP MODAL //// -->
<div id="associateModal" class="modal fade" role="dialog" style="opacity:1!important;">
  <div class="modal-dialog" style="top:12em;">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Asignar repartidor</i></h4>
      </div>
      <div class="modal-body">
        <p>
            <b>
                <i class="fa fa-truck"></i> Asignar a: <?php echo insertElement('select','user_id','','form-control chosenSelect','data-placeholder="Seleccione un repartidor"',$DB->fetchAssoc('admin_user',"admin_id,CONCAT(first_name,' ',last_name) AS name","profile_id=361",'name'),'',' ') ?>
                <?php echo insertElement('hidden','associate',''); ?>
            </b>
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" name="button" class="btn btn-success btnBlue" id="associate_user" data-dismiss="modal">Asignar</button>
      </div>
    </div>
  </div>
</div>
<!-- Help Modal -->