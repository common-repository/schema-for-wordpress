<?php
require_once('../../sfwp/sfwp_path.php');
require_once(ABSPATH.'wp-load.php');
global $wpdb;
$list = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . "sfwp");

//define the available types,and their image.  This affects the image that is seen on the post or page editor.
$available_types = array(
   "itemtype"=>array("vname"=>"ItemType","img"=>"itemtype.png","help"=>"Schema for WP"),		
   "itemprop"=>array("vname"=>"ItemProp","img"=>"itemprop.png","help"=>"Schema for WP"),	
   "closediv"=>array("vname"=>"CloseDIV","img"=>"closediv.png","help"=>"Schema for WP"),
   "closespan"=>array("vname"=>"CloseSPAN","img"=>"closespan.png","help"=>"Schema for WP"),	
 );

?>
<html>
    <head>
        <title>Schema for WordPress</title>
        <script type="text/javascript" src="<?php echo get_option('siteurl')."/wp-includes/js/jquery/jquery.js"?>"></script>
         <script type="text/javascript" src="<?php echo get_option('siteurl')."/wp-includes/js/tinymce/tiny_mce_popup.js?ver=327-1235"?>"></script>
        <script type="text/javascript">
            function do_s() {
              var opt = document.getElementById("sfwp_sel").options[document.getElementById("sfwp_sel").selectedIndex];
              var continue_send = true;
			  
            if(opt.value != "0")
				{
					
				var img = opt.id;
				var html = "<img src='<?php echo + get_option('siteurl'); ?>/wp-content/plugins/schema-for-wordpress/resources/i/"+img+"' class='sfwpitem mceItem' title='sfwp id=" + opt.value + " img="+img+"' />";
				<!--var html = "[sfwp id=" + opt.value + "]"; -->
	
				}
        	else
				{
					var html = "";
					alert("Please select something!");
					return false;
				}
        if(continue_send) {
        var win = window.dialogArguments || opener || parent || top;
        win.send_to_editor(html);
        tinyMCEPopup.close();
        return false;
        }
        }
        jQuery(document).ready(function(ex){         
          jQuery("#sfwp_create_link").click(function(){
            jQuery("#insert_wrapper").slideUp(200);
            jQuery("#add_wrapper").slideDown(200);
            return false;
          });
          jQuery("#sfwp_insert_link").click(function(ex){            
            jQuery("#add_wrapper").slideUp(200);
            jQuery("#insert_wrapper").slideDown(200);
            return false;
          });          
          jQuery("#sfwp_frame_type").change(function(){
        var sel = jQuery(this).children("option:selected");
        jQuery("span#frame_type_help").text(sel.attr("help"));
          });
         
         jQuery("#sfwp_frame_add_do").click(function(){
          if(!check_add_form()){
            alert("Please fill in all mandatory  values!\n(note: mandatory fields are marked with a star sign;eg: *)");
            return;
          }
          
          jQuery.post(
                                "<?php echo get_option('siteurl'); ?>/wp-content/plugins/schema-for-wordpress/resources/tinymce/sfwp_ajax_add.php",
                                {name:jQuery("#sfwp_frame_name").val(),
                                type:jQuery("#sfwp_frame_type").val(),
                                description:jQuery("#sfwp_frame_description").val(),
                                content:jQuery("#sfwp_frame_content").val()
                                },
                                function(data){
                                   var new_option = "<option id='"+data.img+"' value='"+data.id+"'>"+data.name+"</option>";
                                   jQuery("#sfwp_sel").append(new_option);                                   
                                  document.getElementById("sfwp_sel").selectedIndex = parseInt(jQuery("#sfwp_sel option").length) -1;
                                  jQuery("#add_wrapper").slideUp(200);
                                  jQuery("#insert_wrapper").slideDown(200);
                                }, 
                                "json"
                            );
          
         });
          
        });
        
        function check_add_form(){
          var f1 = jQuery("#sfwp_frame_name").val().length;
          var f2 = jQuery("#sfwp_frame_content").val().length;
          if(!f1 || !f2){
          return false;
          }
          return true;
        }
    </script>
        <style>
            body {font-family: sans-serif;font-size:10pt;}
            #add_wrapper {display:none;}
            #sfwp_add_new_form_wrap_ajax label,
            #sfwp_add_new_form_wrap_ajax input,
            #sfwp_add_new_form_wrap_ajax select,
            #sfwp_add_new_form_wrap_ajax textarea {clear:both;float:left;}
            #sfwp_add_new_form_wrap_ajax p {margin:2px 0;border-top:1px solid #ccc;float:left;width:100%;}
            #sfwp_add_new_form_wrap_ajax input {line-height:10pt;height:25px;width:300px;}
            #sfwp_add_new_form_wrap_ajax select {line-height:10pt;height:25px;}
            #sfwp_add_new_form_wrap_ajax label {font-weight:bold;}
           #frame_type_help {float:right;padding-right:10px;}
        </style>
    </head>
    <body>



<div id="insert_wrapper">
<br /><br /><br /><br />
<h4>Insert Schema for WordPress</h4>

<br /><br /><br />
<select id="sfwp_sel" name="sfwp_sel" style="width:300px;">
    <option value="0">--Select a Schema--</option>
    <?php foreach($list as $l): ?>
        <option id="<?php echo $available_types[$l->type]["img"]?>" value="<?php echo $l->id;?>"><?php echo $l->name; ?></option>
    <?php endforeach; ?>
</select>

<br /><br /><br /><br />
<button id="sfwp_insert" onClick="do_s()">Insert Schema</button>
<br /><br />

  
</div>
</div>
    </body>
</html>