jQuery(document).ready(function() {

    jQuery("#sfwp_toggle").click(function(){
        if(jQuery(this).attr("checked")) {
            jQuery(".sfwp_del_cb").each(function(){
                jQuery(this).attr("checked",true);
        });
        }
        else
            {
                jQuery(".sfwp_del_cb").each(function(){
                jQuery(this).attr("checked",false);
        });
            }
    });

    var cb = jQuery("#unin");
    if(cb.length) {
        cb.click(function(){
           if(confirm("Are you sure? \nYou will permanently lose your schema when the plugin is deactivated.")) {
           cb.unbind("click");
           return true;
           }
           else
               {
                   return false;
               }
        });
    }



    jQuery("#sfwp_type").change(function(){
        var sel = jQuery(this).children("option:selected");
        jQuery("span#type_help").text(sel.attr("help"));
        jQuery("img#type_img").attr("src","../wp-content/plugins/schema-for-wordpress/resources/i/"+sel.attr("img"));
    });

    jQuery(".sfwp_export").each(function(){
        jQuery(this).click(function(evt){
        evt.preventDefault();
        var blocks_to_export = [];
        jQuery(".sfwp_del_cb").each(function(){
            if(jQuery(this).attr("checked")==true) blocks_to_export.push(jQuery(this).val());
        });

        if(blocks_to_export.length)
            {               
                location.href="../wp-content/plugins/schema-for-wordpress/sfwp/sfwp_export.php?sfwp="+blocks_to_export.join(";");
            }
            else
                {
                    alert("Select at least one block to export!");
                }
    });
    });

});
