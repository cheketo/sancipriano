<!-- Associate Modal Trigger -->
<!-- //// ASSOCIATE MODAL //// -->
<div id="associateModal" class="modal fade" role="dialog" style="opacity:1!important;">
  <div class="modal-dialog" style="top:12em;">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Asociar orden</i></h4>
      </div>
      <div class="modal-body" id="AssocBody">
        <!--<p>-->
        <!--    <b>-->
        <!--        <i class="fa fa-truck"></i> Asignar a: <?php echo insertElement('select','user_id','','form-control chosenSelect','data-placeholder="Seleccione un repartidor"',$DB->fetchAssoc('admin_user a INNER JOIN relation_admin_group b ON (a.admin_id=b.admin_id)',"a.admin_id,CONCAT(a.first_name,' ',a.last_name) AS name","b.group_id=1 AND a.status='A'",'name'),'',' ') ?>-->
        <!--        <?php echo insertElement('hidden','associate',''); ?>-->
        <!--    </b>-->
        <!--</p>-->
      </div>
      <div class="modal-footer">
        <button type="button" name="button" class="btn btn-success btnBlue" id="associate_user" data-dismiss="modal">Asignar</button>
      </div>
    </div>
  </div>
</div>
<!-- Associate Modal -->