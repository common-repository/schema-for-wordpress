<?php

class sfwp
{
    public  $table_name;



    function  __construct() {
        global $wpdb;
        $this->table_name=$wpdb->prefix . "sfwp";

        

    }

    public static function main_page($message = "") {
         global $wpdb;
	$view= isset($_GET['view']) ? $_GET['view']:"";
        $r = "";

        //define the available types,and their image
        $available_types = array(
            "itemtype"=>array("vname"=>"ItemType","img"=>"itemtype.png","help"=>"Schema for WP"),
            "itemprop"=>array("vname"=>"ItemProp","img"=>"itemprop.png","help"=>"Schema for WP"),
            "closediv"=>array("vname"=>"CloseDIV","img"=>"closediv.png","help"=>"Schema for WP"),
            "closespan"=>array("vname"=>"CloseSPAN","img"=>"closespan.png","help"=>"Schema for WP"),				
            );

        ob_start();
        ?>
<div class="wrap">



    <h2>Schema for WordPress</h2>
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="vertical-align:top;">
                
        <p class="description">
        Schema for WordPress lets you add consistent schema markup code to enhance your site's content display on the main search engines: Google, Yahoo and Bing.  For more details on how to use Schema on your site, please visit <a href="http://schema.org" target="_blank">Schema.org</a>.</p>
        <p>Schema are added to Pages and Posts by clicking on the <img border="0" src="<?php echo get_option('siteurl');?>/wp-content/plugins/schema-for-wordpress/resources/tinymce/sfwp-logo.gif" /> icon in the editor toolbar and selecting the Schema to insert. <!--Once you become familiar with the numbers of the schema you use most frequently you may find it quicker to add the shortcode  directly into the content in Visual or HTML mode. --></p>
        <p>This product is currently in Beta and full documentation will be added on the <a href="http://schemaforwordpress.com" target="_blank">Schema for WordPress</a> web site.  Register to join the <a href="http://schemaforwordpress.com/sfwp-forum" target="_blank">discussion forum</a> and follow us on <a href=" http://twitter.com/intent/user?screen_name=SchemaForWP" target="_blank">Twitter</a> for the latest news and updates.</p>
        <p>Please note that this plugin does not guarantee SEO results; you should keep up to date with the Schema techniques discussed on the plugin forum and the schema.org web site.<p>
        <p>The plugin download includes a file which needs to be uploaded; during beta testing, this will require a SQL import but we are working on a dashboard option.  For the latest version of this file please check the forums or watch out for Tweets when new schema are added.</p>
        <p>Schema that you do not use can be deleted to reduce the size of the selection when creating pages and posts</p>
        <p>Schema can be edited by clicking on the name.  Please note that each one contains a comment string to identify it as Schema for WordPress.  This can be removed but it is a useful tool to help us de-bug any problems you may have so you are encouraged to leave it in place.</p>
        <p>Please report any bugs, suggestions for improvements or feature requests by leaving a comment on the <a href="http://schemaforwordpress.com" target="_blank">Schema for WordPress plugin homepage</a>.</p>
    <div class="tablenav">
	<!--<div class="alignleft actions">
		<a class="button" href="<?php echo get_option('siteurl').'/wp-admin/options-general.php?page=schema-for-wp&view=manage';?>">Manage Schema</a> |
		<a class="button" href="<?php echo get_option('siteurl').'/wp-admin/options-general.php?page=schema-for-wp&view=addnew';?>">Add a New Schema</a>
        </div> -->

			</div>
            <form name="sfwp_import" action="" method="POST" enctype="multipart/form-data">
        <table class="widefat" style="margin-top: .5em">
                <thead>
			<tr valign="top">
			 <th colspan="2" bgcolor="#DDD">Import Schema for WordPress</th>
			</tr>

                        <tr>
                         <td>
                             <input tabindex="16" type="submit" name="sfwp_import" class="button-primary" value="Import" />
                        </td>
			<th  scope="row" width="85%" style="font-weight:normal;">
                            <input type="file" name="sfwp_import_file" />
                        </th>

                        </tr>

                </thead>
        </table>
             </form>
             <br />
             <h3>Schema Currently Available</h3>
    <?php if ( strlen($message) ) : ?>
        <div id="message" class="updated fade"><p><strong><?php echo $message; ?></strong></p></div>
    <?php endif; ?>
<?php if ($view == "" OR $view == "manage") : ?>
    <?php

        $like_list = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . "sfwp");
    ?>
        <form action="" name="del_sfwp" method="POST" onsubmit="return confirm('Are you sure?');">
    <table class="widefat" style="margin-top: .5em">
	<thead>
            <tr>
              <td colspan="4">
                 
                <input tabindex="15" type="submit" name="sfwp_delete" class="button-primary" value="Delete selected" />
               <!-- <a class="sfwp_export button-primary" href="#">Export selected</a> -->
             </td>
         </tr>
	  <tr>
		<th scope="col" class="check-column"><input type="checkbox" name="sfwp_toggle" id="sfwp_toggle" /></th>
		<th scope="col" width="2%"><center>ID</center></th>
		<th scope="col" width="30%">Name</th>
		<th scope="col" width="40%">Description</th>
                <th scope="col" width="10%">Type</th>
                <th scope="col" width="15%">Shortcode</th>
	  </tr>
	 </thead>
         <?php if(count($like_list)): ?>
         <?php $class='';?>
         <?php foreach($like_list as $ll) :?>
         <?php
            if($class != 'alternate') {$class = 'alternate';} else {$class = '';}
         ?>
         <tr class='<?php echo $class; ?>'>
             <th scope="row" class="check-column"><input class="sfwp_del_cb" type="checkbox" name="sfwp_del[]" value="<?php echo $ll->id; ?>" /></th>
             <td><center><?php echo $ll->id;?></center></td>
             <td><strong><a class="row-title" href="<?php echo get_option('siteurl').'/wp-admin/options-general.php?page=schema-for-wp&view=update&edid='.$ll->id;?>" title="Edit"><?php echo stripslashes($ll->name);?></a></strong></td>
             <td><?php echo stripslashes(html_entity_decode($ll->description));?></td>
             <td><?php echo $available_types[$ll->type]["vname"];?></td>
             <td>[sfwp id=<?php echo $ll->id; ?>]</td>
         </tr>
         <?php endforeach; ?>
         <tr>
             <td colspan="4">
                 <input tabindex="15" type="submit" name="sfwp_delete" class="button-primary" value="Delete selected" />
                 <!--<a class="sfwp_export button-primary" href="#">Export selected</a> -->
             </td>
         </tr>
         <?php else: ?>
         <tr id='no-groups'>
		<th scope="row" class="check-column">&nbsp;</th>
		<td colspan="4"><em>No Schema yet!</em></td>
	</tr>
         <?php endif;?>
    </table>
</form>
        <div style="height:50px;">&nbsp;</div>

        

        <div style="height:50px;">&nbsp;</div>
        <form name="complete_uninstall" action="" method="POST">
        <table class="widefat" style="margin-top: .5em">
                <thead>
			<tr valign="top">
			 <th colspan="2" bgcolor="#DDD">Uninstall Schema for WordPress</th>
			</tr>

                        <tr>
                         <td>
                            <?php
                                $check_uninstall =  (get_option("sfwp_complete_uninstall","no")=="yes") ? "checked":"id='unin'";
                            ?>
                             <input tabindex="15" type="submit" name="sfwp_unin" class="button-primary" value="Update" />
                             &nbsp;
                            <input type="checkbox" name="ch_unin" value="1" <?php echo $check_uninstall;?> />

                        </td>
			<th  scope="row" width="85%" style="font-weight:normal;">
                            Checking this box will remove all Schema and the table from the database.
                            Only use if permanently uninstalling the Schema for WordPress plugin.
                        </th>

                        </tr>

                </thead>
        </table>
             </form>

    <?php elseif($view=="addnew" || $view=="update"): ?>
        <h3>Add a New schema</h3>
        <form method="post" action="options-general.php?page=schema-for-wp">
            <?php $val = "";?>
            <?php if($view=="update"):?>
            <?php
                $edit_id = $_GET["edid"];
                $record = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."sfwp WHERE id = %d", $edit_id));

                //$val = $record->type="php" ? base64_decode($record->value):$record->value;
            ?>
            <input type="hidden" name="update_it" value="<?php echo $edit_id;?>" />
            <?php else: ?>
            <?php
              //define a empty object(or one with default values)
              $record = new stdClass();
              $record->name = "";
              $record->type = "";
              $record->description = "";
              $record->value = "";
            ?>
            <?php endif; ?>
            <p style="color:#ff0000;">* required</p>
            
          <table class="widefat" style="margin-top: .5em">
                <thead>
			<tr valign="top">
			 <th colspan="4" bgcolor="#DDD">Enter the details of your schema and click the Save button</th>
			</tr>

                        <tr>
			  <th scope="row" width="200">Name (short title) <span style="color:#ff0000;">*</span></th>
			<td colspan="3">
                            
                <input tabindex="1" name="sfwp_name" type="text" size="68" maxlength="36" class="search-input" value="<?php echo stripslashes($record->name);?>" autocomplete="off" />
              </td>
			</tr>

                        <tr>
			  <th scope="row" width="200">Type <?php
                                $actual_type = $view=="addnew" ? "other":$record->type;
                            ?>
                            </th>
			<td colspan="3">
                            <select name="sfwp_type" id="sfwp_type">
                                <?php foreach($available_types as $ak=>$av): ?>
                                <option<?php echo ($record->type==$ak ? " selected":""); ?> value="<?php echo $ak?>" img="<?php echo $av["img"]?>" help="<?php echo $av["help"]?>"><?php echo $av["vname"]?></option>
                                <?php endforeach; ?>
                            </select>
                            &nbsp;
                           
                            &nbsp;
                            <span id="type_help"><?php echo $available_types[$actual_type]["help"];?></span>
                        </td>
			</tr>

                         <tr>
			  <th scope="row" valign="top" style="border-bottom:none;" width="200">Description
			                 <br />
                            <span style="font-weight:normal;">
                            (Optional)</span>

			</th>
			  <td colspan="3" rowspan="1">
                            
                <textarea tabindex="2" name="sfwp_description" cols="55" rows="3"><?php echo htmlspecialchars_decode(stripslashes($record->description));?></textarea>
              </td>
			</tr>

                        <tr>
			  <th scope="row" valign="top" style="border-bottom:none;" width="200">
                            Content <span style="color:#ff0000;">*</span>
                            <br />
                            <span style="font-weight:normal;">
                            Enter or paste the content to appear in your schema
                            </span>
                        </th>
			<td colspan="3" rowspan="1">
                            
                <textarea tabindex="2" name="sfwp_value" cols="55" rows="10"><?php echo htmlspecialchars_decode(stripslashes($record->value));?></textarea>
              </td>
			</tr>

		</thead>
            </table>
            <?php if($view=="update"):?>
            <?php endif; ?>
            <p class="submit">
		<input tabindex="15" type="submit" name="sfwp_save" class="button-primary" value="Save" />
		<a href="options-general.php?page=schema-for-wp&view=manage" class="button">Cancel</a>
	    </p>

        </form>
    <?php endif; ?>
            </td>
            
      <td width="210" style="width:210px;vertical-align:top;text-align:right; " valign="top">
                
        
                
            </td>
        </tr>
    </table>
    
    
        
</div>
        <?php
        $r = ob_get_contents();
        ob_end_clean();
        return $r;
    }

   
}

?>