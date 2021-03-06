<?php
/*
$Id: product_reviews_write.js.php 1692 2012-02-26 01:26:50Z michael.oscmax@gmail.com $

  osCmax e-Commerce
  http://www.oscmax.com

  Copyright 2000 - 2011 osCmax

  Released under the GNU General Public License
*/
?>
<script src="http://www.google.com/jsapi"></script>
<script>
  // Load jQuery
  google.load("jquery", "1.4.0");
</script>
<script type="text/javascript" src="includes/javascript/slimbox2/slimbox2.js"></script>
<?php include(DIR_WS_INCLUDES . 'javascript/sbcustom.php'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo (bts_select('stylesheet','slimbox2.css')); // BTSv1.5 ?>">
<script type="text/javascript" src="includes/javascript/jquery-ui.stars.custom.js"></script>
<script type="text/javascript" src="includes/javascript/jquery.ui.stars.min.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
   $("#stars-wrapper1").stars({
     captionEl: $("#stars-cap")
   });
});
</script>

<script language="javascript" type="text/javascript"><!--
function checkForm() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  var review = document.product_reviews_write.review.value;

  if (review.length < <?php echo REVIEW_TEXT_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_REVIEW_TEXT; ?>";
    error = 1;
  }

  if ((document.product_reviews_write.rating[0].checked) || (document.product_reviews_write.rating[1].checked) || (document.product_reviews_write.rating[2].checked) || (document.product_reviews_write.rating[3].checked) || (document.product_reviews_write.rating[4].checked)) {
  } else {
    error_message = error_message + "<?php echo JS_REVIEW_RATING; ?>";
    error = 1;
  }

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}

function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>