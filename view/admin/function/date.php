<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" rel="stylesheet"/>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/locales/bootstrap-datepicker.th.min.js"></script>


<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title text-center" style="color:#F0592E;">วันที่ดูย้อนหลัง</h5>
          <hr>
          <div class="form-group">
            <label for="datepicker"><strong>เลือกวันที่:</strong></label>
            <div class="input-group date">
              <input id="datepicker" class="form-control datepicker" placeholder="เลือกวันที่">
              <div class="input-group-append">
                <span class="input-group-text">
                  <i class="fa fa-calendar"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($){
        jQuery.fn.datepicker.defaults.language = 'th';
        jQuery('#datepicker').datepicker({
                    autoclose: true,
                    todayHighlight: true
                }).datepicker('update', new Date());
    });
</script>
