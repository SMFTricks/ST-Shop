<?php

/**
 * @package SA Shop
 * @version 2.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2014, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

function template_Shop_adminInfo()
{
	global $context, $scripturl, $txt;

	// Welcome message for the admin.
	echo '
	<div id="admincenter">';

	// Is there an update available?
	echo '
		<div id="update_section"></div>
		<div id="admin_main_section">';

	// Display the "live news" from smftricks.com.
	echo '
			<div id="live_news" class="floatleft">
				<div class="cat_bar">
					<h3 class="catbg">
						<span class="ie6_header floatleft">', $txt['Shop_live_news'] , '</span>
					</h3>
				</div>
				<div class="windowbg nopadding">
					<div id="smfAnnouncements">', $txt['lfyi'], '</div>
				</div>
			</div>';

	// Show the Breeze version.
	echo '
			<div id="supportVersionsTable" class="floatright">
				<div class="cat_bar">
					<h3 class="catbg">
						', $txt['support_title'], '
					</h3>
				</div>
				<div class="windowbg nopadding">
					<div class="content padding">
						<div id="version_details">
							<strong>', $txt['support_versions'], ':</strong><br />
							', $txt['Shop_version'] , ':
							<em id="yourVersion" style="white-space: nowrap;">', $context['Shop']['version'] , '</em><br />';

		// Some more stuff will be here... eventually

	echo '
						</div>
					</div>
					<div class="title_bar">
						<h4 class="titlebg">', $txt['Shop_donate_title'], '
					</div>
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="text-align: center">
						<br>
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="YP3KXRJ2Q3ZJU">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
				</div>
			</div>
			<br class="clear" /><br>
			<div class="cat_bar">
				<h3 class="catbg">
					<span class="ie6_header floatleft">', $txt['Shop_main_credits'], '</span>
				</h3>
			</div>
			<div class="windowbg nopadding"><div class="padding">';

	// Print the credits array
	if (!empty($context['Shop']['credits']))
		foreach ($context['Shop']['credits'] as $c)
		{
			echo '
				<dl>
					<dt>
						<strong>', $c['name'], '</strong>
					</dt>';

			foreach ($c['users'] as $u)
				echo '
						<dd>
							<a href="', $u['site'] ,'">', $u['name'] ,'</a>', (isset($u['desc']) ? ' - <span class="smalltext">'. $u['desc']. '</span>' : ''), '
						</dd>';
			echo '
					</dl>';
		}

	echo '
			</div></div>
		</div>
		<br class="clear" />';

	foreach ($context[$context['admin_menu_name']]['sections'] as $area_id => $area)
	{
		// Only shop info...
			if ($area_id != 'shop')
				continue;

		echo '
		<fieldset id="group_', $area_id, '" class="windowbg admin_group">
			<legend>', $area['title'], '</legend>';

		foreach ($area['areas'] as $item_id => $item)
		{
			// Don't show home
			if ($item_id == 'shopinfo')
				continue;

			$url = isset($item['url']) ? $item['url'] : $scripturl . '?action=admin;area=' . $item_id . (!empty($context[$context['admin_menu_name']]['extra_parameters']) ? $context[$context['admin_menu_name']]['extra_parameters'] : '');

			if (!empty($item['icon_file']))
				echo '
			<a href="', $url, '" class="admin_group', !empty($item['inactive']) ? ' inactive' : '', '"><img class="large_admin_menu_icon_file" src="', $item['icon_file'], '" alt="">', $item['label'], '</a>';
			else
				echo '
			<a href="', $url, '"><span class="large_', $item['icon_class'], !empty($item['inactive']) ? ' inactive' : '', '"></span>', $item['label'], '</a>';
		}

		echo '
		</fieldset>';
	}

	echo '
	</div>';
}

function template_Shop_itemsDelete()
{
	global $context, $txt, $scripturl, $boardurl;

	echo '
	<div class="windowbg">
		<form method="post" action="', $scripturl, '?action=admin;area=shopitems;sa=delete2">
			<h2>', $txt['Shop_sure_delete'], '</h2>
			<span class="smalltext">', $txt['Shop_item_delete_also'], '</span>
			<br />
			<hr />
			<ul>';

		// Loop through each item chosen to delete...
		foreach ($context['shop_items_delete'] as $del)
			// and output them to the page, along with a hidden input field (so we know what id's to delete)
			echo '
				<li>
					<input type="hidden" name="delete[]" value="', $del['id'], '" /> <img src="', $boardurl . $context['items_url'] . $del['image'], '" alt="" style="', $context['itemOpt'], ' vertical-align: middle;" />&nbsp; &nbsp; &nbsp; ', $del['name'], '
					<hr />
				</li>';

		echo '
			</ul>
			<input class="button floatleft" type="submit" value="', $txt['delete'], '" />
			<input class="button floatleft" type="button" value="', $txt['Shop_no_goback'], '" onclick="window.location=\'', $scripturl, '?action=admin;area=shopitems\'" />
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		</form>
	</div>';

}

function template_Shop_itemsAdd()
{
	global $context, $txt, $scripturl;

	echo '
	<div class="windowbg">
		<form method="post" action="' . $scripturl . '?action=admin;area=shopitems;sa=add2">
			', $txt['Shop_want_module'], ': 
			<input class="input_check" type="checkbox" name="module" value="1" onclick="document.getElementById(\'SelectModule\').style.display = this.checked ? \'block\' : \'none\';"', (empty($context['shop_modules']) ? ' disabled="disabled" ' : ''), ' /><br/>
			<span class="smalltext">', $txt['Shop_item_not_module'], '</span>';

		// Do we actually have any modules in the shop?
		if (!empty($context['shop_modules']))
		{
			echo '
			<fieldset id="SelectModule" style="display: none; border: none; margin: 10px -8px -25px; width: 0;">
				<select name="item">
					<optgroup label="', $txt['Shop_item_module_select'], '">';

				// For every module that's possible to add...
				foreach ($context['shop_modules'] as $module)
				echo '
						<option value="', $module['id'], '">', $module['name'], ' by ', $module['author'], ' &lt;', $module['email'], '&gt;</option>';

			echo '
					</optgroup>
				</select>
			</fieldset>';
		}

		echo '
			<input class="button floatleft" type="submit" value="', $txt['Shop_item_add'], '" />
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		</form>

	</div>';
}

function template_Shop_itemsAdd2()
{
	global $context, $txt, $scripturl, $modSettings, $boardurl;

	echo '
	<div class="windowbg">
		<form method="post" action="' . $scripturl . '?action=admin;area=shopitems;sa=add3" name="Shopitemsadd">
			<input type="hidden" name="item" value="', $context['shop_item_info']['name'], '" />
			<input type="hidden" name="require_input" value="', $context['shop_item_info']['require_input'], '" />
			<input type="hidden" name="can_use_item" value="', $context['shop_item_info']['can_use_item'], '" />
			<input type="hidden" name="module" value="', $context['shop_item_info']['module'], '" />
			<dl class="settings">
				<dt>
					<a id="setting_itemname"></a>
					<span><label for="itemname">', $txt['Shop_item_name'], ':</label></span>
				</dt>
				<dd>
					<input class="input_text" name="itemname" id="itemname" type="text" value="', $context['shop_item_info']['friendlyname'], '" style="width: 100%" />
				</dd>
				<dt>
					<a id="setting_itemdesc"></a>
					<span><label for="itemdesc">', $txt['Shop_item_description'], ':</label></span>
				</dt>
				<dd>
					<textarea name="itemdesc" id="itemdesc"  rows="2" style="width: 100%">', $context['shop_item_info']['desc'], '</textarea>
				</dd>
				<dt>
					<a id="setting_itemprice"></a>
					<span><label for="itemprice">', $txt['Shop_item_price'], ':</label></span>
				</dt>
				<dd>
					', !empty($modSettings['Shop_credits_prefix']) ? $modSettings['Shop_credits_prefix']. ' &nbsp;' : '', '<input class="input_text" name="itemprice" id="itemprice" type="number" min="0" value="', $context['shop_item_info']['price'], '" size="5" />&nbsp; ', $modSettings['Shop_credits_suffix'], '
				</dd>
				<dt>
					<a id="setting_itemstatus"></a>
					<span><label for="itemstatus">', $txt['Shop_item_enable'], ':</label></span>
				</dt>
				<dd>
					<input class="input_check" type="checkbox" name="itemstatus" id="itemstatus" value="1" />
				</dd>
			</dl>
			<dl class="settings">
				<dt>
					<a id="setting_itemstock"></a>
					<span><label for="itemstock">', $txt['Shop_item_stock'], ':</label></span>
				</dt>
				<dd>
					<input class="input_text" name="itemstock" id="itemstock" type="number" min="0" value="', $context['shop_item_info']['stock'], '" size="5" />
				</dd>
				<dt>
					<a id="setting_itemlimit"></a>
					<span><label for="itemlimit">', $txt['Shop_item_limit'], ':</label></span><br />
					<span class="smalltext">', $txt['Shop_item_limit_desc'], '</span>
				</dt>
				<dd>
					<input class="input_text" name="itemlimit" id="itemlimit" type="number" min="0" value="', $context['shop_item_info']['itemlimit'], '" size="5" />
				</dd>
			</dl>
			<dl class="settings">
				<dt>
					<a id="setting_cat"></a>
					<span><label for="cat">', $txt['Shop_item_category'], ':</label></span>
				</dt>
				<dd>
					<select name="cat" id="cat">
						<option value="0">', $txt['Shop_item_uncategorized'], '</option>';

					if (!empty($context['shop_categories_list']))
					{
						echo '
						<optgroup label="', $txt['Shop_item_category_select'], '">';

						// List the categories if there are
						foreach ($context['shop_categories_list'] as $category)
						echo '
							<option value="', $category['id'], '">', $category['name'], '</option>';

					echo '
						</optgroup>';

					}

				echo '
					</select>
				</dd>
				<dt>
					<a id="setting_icon"></a>
					<span><label for="icon">', $txt['Shop_item_image'], ':</label></span>
				</dt>
				<dd>
					<!-- TODO: Should JavaScript detect Sources URL? -->
					<script type="text/javascript" language="javascript">
					<!--
						function show_image()
						{
							if (document.Shopitemsadd.icon.value !== "none")
							{
								// TODO: Should this detect the sources URL, rather than just assume?
								var image_url = "', $boardurl . $context['items_url'], '" + document.Shopitemsadd.icon.value;
								document.images["icon"].src = image_url;
							}
							else
							{
								document.images["icon"].src = "', $boardurl . $context['items_url'], 'blank.gif";
							}
						}
					//-->
					</script>
					<select name="icon" id="icon" onchange="show_image()">
						<optgroup label="', $txt['Shop_item_image_select'], '">
							<option value="blank.gif" selected="selected">', $txt['Shop_items_none_select'], '</option>';

						// List the images
						foreach ($context['shop_images_list'] as $image)
						echo '
							<option value="', $image, '">', $image, '</option>';

					echo '
						</optgroup>
					</select>
					&nbsp;&nbsp;<img name="icon" src="', $boardurl . $context['items_url'], 'blank.gif" alt="" style="', $context['itemOpt'], ' border: 1px solid rgba(0,0,0,0.2);" /><br />
					<span class="smalltext">', $txt['Shop_item_notice'], '</span>
				</dd>
			</dl>';

		if (isset($context['shop_item_info']['addInput']) && (($context['shop_item_info']['addInput'] != '') && ($context['shop_item_info']['addInput'] == true)))
		echo '
			<hr />
			<a id="addinput"></a>
			<span><label for="addinput"><strong>', $txt['Shop_item_additional'], '</strong></label></span><br />
			<span class="smalltext">', $txt['Shop_item_description_match'], '</span>
			', $context['shop_item_info']['addInput'], '';

		if (isset($context['shop_item_info']['can_use_item']) && (($context['shop_item_info']['can_use_item'] != '') && ($context['shop_item_info']['can_use_item'] == true)))
		echo '
			<dl class="settings">
				<dt>
					<a id="setting_itemdelete"></a>
					<span><label for="itemdelete">', $txt['Shop_item_delete_after'], ':</label></span>
				</dt>
				<dd>
					<input class="input_check" type="checkbox" name="itemdelete" id="itemdelete" ', ($context['shop_item_info']['delete_after_use'] ? ' checked' : ''), '/>
				</dd>
			</dl>';

		echo '
			<input class="button floatleft" type="submit" value="', $txt['Shop_item_add_items'], '" />
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		</form>
	</div>';

}

function template_Shop_itemsEdit()
{
	global $context, $txt, $scripturl, $modSettings, $boardurl;

	echo '
	<div class="windowbg">
		<form method="post" action="' . $scripturl . '?action=admin;area=shopitems;sa=edit2" name="Shopitemsedit">
			<input type="hidden" name="id" value="', $context['shop_item_edit']['itemid'], '" />
			<dl class="settings">
				<dt>
					<a id="setting_itemname"></a>
					<span><label for="itemname">', $txt['Shop_item_name'], ':</label></span>
				</dt>
				<dd>
					<input class="input_text" name="itemname" id="itemname" type="text" value="', $context['shop_item_edit']['name'], '" style="width: 100%" />
				</dd>
				<dt>
					<a id="setting_itemdesc"></a>
					<span><label for="itemdesc">', $txt['Shop_item_description'], ':</label></span>
				</dt>
				<dd>
					<textarea name="itemdesc" id="itemdesc"  rows="2" style="width: 100%">', $context['shop_item_edit']['desc'], '</textarea>
				</dd>
				<dt>
					<a id="setting_itemprice"></a>
					<span><label for="itemprice">', $txt['Shop_item_price'], ':</label></span>
				</dt>
				<dd>
					', !empty($modSettings['Shop_credits_prefix']) ? $modSettings['Shop_credits_prefix']. ' &nbsp;' : '', '<input class="input_text" name="itemprice" id="itemprice" type="number" min="0" value="', $context['shop_item_edit']['price'], '" size="5" />&nbsp; ', $modSettings['Shop_credits_suffix'], '
				</dd>
				<dt>
					<a id="setting_itemstatus"></a>
					<span><label for="itemstatus">', $txt['Shop_item_enable'], ':</label></span>
				</dt>
				<dd>
					<input class="input_check" type="checkbox" name="itemstatus" id="itemstatus" value="1"', ($context['shop_item_edit']['status'] ? ' checked' : ''), '/>
				</dd>
			</dl>
			<dl class="settings">
				<dt>
					<a id="setting_itemstock"></a>
					<span><label for="itemstock">', $txt['Shop_item_stock'], ':</label></span>
				</dt>
				<dd>
					<input class="input_text" name="itemstock" id="itemstock" type="number" min="0" value="', $context['shop_item_edit']['stock'], '" size="5" />
				</dd>
				<dt>
					<a id="setting_itemlimit"></a>
					<span><label for="itemlimit">', $txt['Shop_item_limit'], ':</label></span><br />
					<span class="smalltext">', $txt['Shop_item_limit_desc'], '</span>
				</dt>
				<dd>
					<input class="input_text" name="itemlimit" id="itemlimit" type="number" min="0" value="', $context['shop_item_edit']['itemlimit'], '" size="5" />
				</dd>
			</dl>
			<dl class="settings">
				<dt>
					<a id="setting_cat"></a>
					<span><label for="cat">', $txt['Shop_item_category'], ':</label></span>
				</dt>
				<dd>
					<select name="cat" id="cat">
						<option value="0">', $txt['Shop_item_uncategorized'], '</option>';

					if (!empty($context['shop_categories_list'])) {
						echo '
						<optgroup label="', $txt['Shop_item_category_select'], '">';

						// List the categories
						foreach ($context['shop_categories_list'] as $category)
						echo '
							<option value="', $category['id'], '"', ($context['shop_item_edit']['catid'] == $category['id'] ? ' selected="selected"' : ''), '>', $category['name'], '</option>';

					echo '
						</optgroup>';
					}

				echo '
					</select>
				</dd>
				<dt>
					<a id="setting_icon"></a>
					<span><label for="icon">', $txt['Shop_item_image'], ':</label></span>
				</dt>
				<dd>
					<!-- TODO: Should JavaScript detect Sources URL? -->
					<script type="text/javascript" language="javascript">
					<!--
						function show_image()
						{
							if (document.Shopitemsedit.icon.value !== "none")
							{
								// TODO: Should this detect the sources URL, rather than just assume?
								var image_url = "', $boardurl . $context['items_url'], '" + document.Shopitemsedit.icon.value;
								document.images["icon"].src = image_url;
							}
							else
							{
								document.images["icon"].src = "', $boardurl . $context['items_url'], 'blank.gif";
							}
						}
					//-->
					</script>
					<select name="icon" id="icon" onchange="show_image()">
						<optgroup label="', $txt['Shop_item_image_select'], '">
							<option value="blank.gif"', ($context['shop_item_edit']['image'] == 'blank.gif' ? ' selected="selected"' : ''), '>', $txt['Shop_items_none_select'], '</option>';

						// List the images
						foreach ($context['shop_images_list'] as $image)
						echo '
							<option value="', $image, '"', ($context['shop_item_edit']['image'] == $image ? ' selected="selected"' : ''), '>', $image, '</option>';

					echo '
						</optgroup>
					</select>
					&nbsp;&nbsp;<img name="icon" src="', $boardurl . $context['items_url'], $context['shop_item_edit']['image'], '" alt="" style="', $context['itemOpt'], ' border: 1px solid rgba(0,0,0,0.2);" /><br />
					<span class="smalltext">', $txt['Shop_item_notice'], '</span>
				</dd>
			</dl>';

		if ((($context['shop_item_edit']['addInputEditable'] == true) && isset($context['shop_item_edit']['addInput'])) && (($context['shop_item_edit']['addInput'] != '') && ($context['shop_item_edit']['addInput'] == true)))
		echo '
			<hr />
			<a id="addinput"></a>
			<span><label for="addinput"><strong>', $txt['Shop_item_additional'], '</strong></label></span><br />
			<span class="smalltext">', $txt['Shop_item_description_match'], '</span>
			', $context['shop_item_edit']['addInput'], '';

		if (isset($context['shop_item_edit']['can_use_item']) && (($context['shop_item_edit']['can_use_item'] != '') && ($context['shop_item_edit']['can_use_item'] == true)))
		echo '
			<dl class="settings">
				<dt>
					<a id="setting_itemdelete"></a>
					<span><label for="itemdelete">', $txt['Shop_item_delete_after'], ':</label></span>
				</dt>
				<dd>
					<input class="input_check" type="checkbox" name="itemdelete" id="itemdelete" ', ($context['shop_item_edit']['delete_after_use'] ? ' checked' : ''), '/>
				</dd>
			</dl>';

		echo '
			<input class="button floatleft" type="submit" value="', $txt['Shop_item_save_item'], '" />
			<input class="button floatleft" type="button" value="', $txt['Shop_no_goback2'], '" onclick="window.location=\'', $scripturl, '?action=admin;area=shopitems\'" />
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		</form>
	</div>';

}

function template_Shop_categoriesDelete()
{
	global $context, $txt, $scripturl, $boardurl;

	echo '
	<div class="windowbg">
		<form method="post" action="', $scripturl, '?action=admin;area=shopcategories;sa=delete2">
			<h2>', $txt['Shop_sure_delete_cat'], '</h2>
			<br />
			<dl class="settings">
				<dt>
					', $txt['Shop_cat_delete_also'], '<br />
					<span class="smalltext">', $txt['Shop_cat_delete_also_desc'], '</span>
				</dt>
				<dd>
					<input class="input_check" type="checkbox" value="deleteitems" name="deleteitems" id="deleteitems" />
				</dd>
			</dl>
			<hr />
			<ul>';

		// Loop through each item chosen to delete...
		foreach ($context['shop_categories_delete'] as $del)
			// and output them to the page, along with a hidden input field (so we know what id's to delete)
			echo '
				<li>
					<input type="hidden" name="delete[]" value="', $del['id'], '" /> <img src="', $boardurl . $context['items_url'] . $del['image'], '" alt="" style="', $context['itemOpt'], ' vertical-align: middle;" />&nbsp; &nbsp; &nbsp; ', $del['name'], ' - <span class="smalltext">', $del['description'], '</span>
					<hr />
				</li>';

		echo '
			</ul>
			<input class="button floatleft" type="submit" value="', $txt['delete'], '" />
			<input class="button floatleft" type="button" value="', $txt['Shop_no_goback'], '" onclick="window.location=\'', $scripturl, '?action=admin;area=shopcategories\'" />
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		</form>
	</div>';

}

function template_Shop_categoriesAdd()
{
	global $context, $txt, $scripturl, $modSettings, $boardurl;

	echo '
	<div class="windowbg">
		<form method="post" action="' . $scripturl . '?action=admin;area=shopcategories;sa=add2" name="Shopcategoriesadd">
			<dl class="settings">
				<dt>
					<a id="setting_catname"></a>
					<span><label for="catname">', $txt['Shop_item_name'], ':</label></span>
				</dt>
				<dd>
					<input class="input_text" name="catname" id="catname" type="text" style="width: 100%" />
				</dd>

				<dt>
					<a id="setting_catdesc"></a>
					<span><label for="catdesc">', $txt['Shop_item_description'], ':</label></span>
				</dt>
				<dd>
					<textarea name="catdesc" id="catdesc"  rows="2" style="width: 100%"></textarea>
				</dd>
			</dl>
			<dl class="settings">
				<dt>
					<a id="setting_caticon"></a>
					<span><label for="caticon">', $txt['Shop_item_image'], ':</label></span>
				</dt>
				<dd>
					<!-- TODO: Should JavaScript detect Sources URL? -->
					<script type="text/javascript" language="javascript">
					<!--
						function show_image()
						{
							if (document.Shopcategoriesadd.caticon.value !== "none")
							{
								// TODO: Should this detect the sources URL, rather than just assume?
								var image_url = "', $boardurl . $context['items_url'], '" + document.Shopcategoriesadd.caticon.value;
								document.images["caticon"].src = image_url;
							}
							else
							{
								document.images["caticon"].src = "', $boardurl . $context['items_url'], 'blank.gif";
							}
						}
					//-->
					</script>
					<select name="caticon" id="caticon" onchange="show_image()">
						<optgroup label="', $txt['Shop_item_image_select'], '">
							<option value="blank.gif" selected="selected">', $txt['Shop_items_none_select'], '</option>';

						// List the images
						foreach ($context['shop_images_list'] as $image)
						echo '
							<option value="', $image, '">', $image, '</option>';

					echo '
						</optgroup>
					</select>
					&nbsp;&nbsp;<img name="caticon" src="', $boardurl . $context['items_url'], 'blank.gif" alt="" style="', $context['itemOpt'], ' border: 1px solid rgba(0,0,0,0.2);" /><br />
					<span class="smalltext">', $txt['Shop_item_notice'], '</span>
				</dd>
			</dl>
			<input class="button floatleft" type="submit" value="', $txt['Shop_category_add_category'], '" />
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		</form>
	</div>';
}

function template_Shop_categoriesEdit()
{
	global $context, $txt, $scripturl, $modSettings, $boardurl;

	echo '
	<div class="windowbg">
		<form method="post" action="' . $scripturl . '?action=admin;area=shopcategories;sa=edit2" name="Shopcategoriesedit">
		<input type="hidden" name="id" value="', $context['shop_category_edit']['catid'], '" />
			<dl class="settings">
				<dt>
					<a id="setting_catname"></a>
					<span><label for="catname">', $txt['Shop_item_name'], ':</label></span>
				</dt>
				<dd>
					<input class="input_text" name="catname" id="catname" type="text" value="', $context['shop_category_edit']['name'], '" style="width: 100%" />
				</dd>

				<dt>
					<a id="setting_catdesc"></a>
					<span><label for="catdesc">', $txt['Shop_item_description'], ':</label></span>
				</dt>
				<dd>
					<textarea name="catdesc" id="catdesc"  rows="2" style="width: 100%">', $context['shop_category_edit']['description'], '</textarea>
				</dd>
			</dl>
			<dl class="settings">
				<dt>
					<a id="setting_caticon"></a>
					<span><label for="caticon">', $txt['Shop_item_image'], ':</label></span>
				</dt>
				<dd>
					<!-- TODO: Should JavaScript detect Sources URL? -->
					<script type="text/javascript" language="javascript">
					<!--
						function show_image()
						{
							if (document.Shopcategoriesedit.caticon.value !== "none")
							{
								// TODO: Should this detect the sources URL, rather than just assume?
								var image_url = "', $boardurl . $context['items_url'], '" + document.Shopcategoriesedit.caticon.value;
								document.images["caticon"].src = image_url;
							}
							else
							{
								document.images["caticon"].src = "', $boardurl . $context['items_url'], 'blank.gif";
							}
						}
					//-->
					</script>
					<select name="caticon" id="caticon" onchange="show_image()">
						<optgroup label="', $txt['Shop_item_image_select'], '">
							<option value="blank.gif"', ($context['shop_category_edit']['image'] == 'blank.gif' ? ' selected="selected"' : ''), '>', $txt['Shop_items_none_select'], '</option>';

						// List the images
						foreach ($context['shop_images_list'] as $image)
						echo '
							<option value="', $image, '"', ($context['shop_category_edit']['image'] == $image ? ' selected="selected"' : ''), '>', $image, '</option>';

					echo '
						</optgroup>
					</select>
					&nbsp;&nbsp;<img name="caticon" src="', $boardurl . $context['items_url'], $context['shop_category_edit']['image'], '" alt="" style="', $context['itemOpt'], ' border: 1px solid rgba(0,0,0,0.2);" /><br />
					<span class="smalltext">', $txt['Shop_item_notice'], '</span>
				</dd>
			</dl>
			<input class="button floatleft" type="submit" value="', $txt['Shop_category_save_category'], '" />
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		</form>
	</div>';
}

function template_Shop_itemsUpload()
{
	global $txt, $scripturl;
	
	if (isset($_REQUEST['success']))
		echo '<div class="infobox">', $txt['Shop_item_upload_success'], '</div>';
	elseif (isset($_REQUEST['error']))
		echo '<div class="errorbox">', $txt['Shop_items_upload_error'], '</div>';

	echo '
		<div class="roundframe">
			<form method="post" action="' . $scripturl . '?action=admin;area=shopitems;sa=uploaditems2" name="UploadItem" enctype="multipart/form-data">
				<input type="file" name="newitem" id="newitem" value="newitem" size="40" class="input_file">
				<input type="submit" class="button" value="', $txt['save'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>';
}

function template_Shop_modulesUpload()
{
	global $scripturl, $txt, $context;
	
	if (isset($_REQUEST['success']))
		echo '<div class="infobox">', $txt['Shop_module_upload_success'], '</div>';
	elseif (isset($_REQUEST['error']))
		echo '<div class="errorbox">', $txt['Shop_module_upload_error'], '</div>';

	echo '
		<div class="roundframe">
			<form method="post" action="' . $scripturl . '?action=admin;area=shopmodules;sa=uploadmodules2" name="UploadModule" enctype="multipart/form-data">
				<input type="file" name="newitem" id="newitem" value="newitem" size="40" class="input_file">
				<input type="submit" class="button" value="', $txt['save'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>';
}

function template_Shop_modulesDelete()
{
	global $context, $txt, $scripturl, $boardurl;

	echo '
	<div class="windowbg">
		<form method="post" action="', $scripturl, '?action=admin;area=shopmodules;sa=delete2">
			<h2>', $txt['Shop_module_delete'], '</h2>
			<span class="smalltext">', $txt['Shop_module_delete_also'], '</span>
			<br /><hr />
			<ul>';

		// Loop through each item chosen to delete...
		foreach ($context['shop_modules_delete'] as $del)
			// and output them to the page, along with a hidden input field (so we know what id's to delete)
			echo '
				<li>
					<input type="hidden" name="files[]" value="', $del['file'], '" />
					<input type="hidden" name="delete[]" value="', $del['id'], '" /> &nbsp; &nbsp; &nbsp; ', $del['name'], ' (',$del['file'], ')
					<hr />
				</li>';

		echo '
			</ul>
			<input class="button floatleft" type="submit" value="', $txt['delete'], '" />
			<input class="button floatleft" type="button" value="', $txt['Shop_no_goback'], '" onclick="window.location=\'', $scripturl, '?action=admin;area=shopmodules\'" />
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		</form>
	</div>';

}

function template_adminShop_invSearch()
{
	global $context, $txt, $scripturl;

	echo '
	<div class="roundframe">
		<div class="windowbg">
			<form method="post" action="', $scripturl,'?action=admin;area=shopinventory;sa=search2">
				', $txt['Shop_inventory_member_name'], '
				&nbsp;<input class="input_text" type="text" name="membername" id="membername" size="25" />
				<div id="membernameItemContainer"></div>
				<span class="smalltext">', $txt['Shop_inventory_member_name_desc'], '</span>
				<br /><br />
				<input class="button floatleft" type="submit" value="', $txt['search'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>
	</div>
	<script>
		var oAddMemberSuggest = new smc_AutoSuggest({
			sSelf: \'oAddMemberSuggest\',
			sSessionId: \'', $context['session_id'], '\',
			sSessionVar: \'', $context['session_var'], '\',
			sSuggestId: \'to_suggest\',
			sControlId: \'membername\',
			sSearchType: \'member\',
			sPostName: \'memberid\',
			sURLMask: \'action=profile;u=%item_id%\',
			sTextDeleteItem: \'', $txt['autosuggest_delete_item'], '\',
			sItemListContainerId: \'membernameItemContainer\'
		});
	</script>';
}

function template_Shop_invRestock()
{
	global $context, $txt, $modSettings, $scripturl;

	if (isset($_REQUEST['success']))
		echo '
	<div class="infobox">', $txt['Shop_restock_successful'], '</div>';

	echo '
	<div class="roundframe">
		<div class="windowbg">
			<form method="post" action="', $scripturl,'?action=admin;area=shopinventory;sa=restock2">
				<dl class="settings">
					<dt>
						', $txt['Shop_restock_what'], ':
					</dt>
					<dd>
						<label><input class="input_radio" type="radio" name="whatitems" value="all" checked onclick="document.getElementById(\'SelectItems\').style.display = this.checked ? \'none\' : \'block\';" />', $txt['Shop_restock_all'], '</label><br />
						<label><input class="input_radio" type="radio" name="whatitems" value="selected" onclick="document.getElementById(\'SelectItems\').style.display = this.checked ? \'block\' : \'none\';" />', $txt['Shop_restock_selected'], '</label>
					</dd>
				</dl>

				<dl class="settings" id="SelectItems" style="display: none;">
					<dt>
						', $txt['Shop_restock_select_items'], '
					</dt>
					<dd>
						<div class="profile_user_links">
							<ol>';
						// For every module that's possible to add...
						foreach ($context['shop_select_items'] as $item)
						echo '
								<li><input type="checkbox" name="restockitem[]" value="', $item['id'], '" /> ', $item['image'], ' ', $item['name'], '</li>';
				echo '
							</ol>
						</div>
					</dd>
				</dl>
				<dl class="settings">
					<dt>
						', $txt['Shop_restock_lessthan'], ':<br />
						<span class="smalltext">', $txt['Shop_restock_lessthan_desc'], '</span>
					</dt>
					<dd>
						<input class="input_text" type="number" min="1" name="stock" id="stock" value="5" size="10" />
					</dd>
					<dt>
						', $txt['Shop_restock_amount'], ':
						<span class="smalltext">', $txt['Shop_restock_amount_desc'], '</span>
					</dt>
					<dd>
						<input class="input_text" type="number" min="1" name="add" id="add" value="30" size="10" />
					</dd>
				</dl>
				<input class="button floatleft" type="submit" value="', $txt['go'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>
	</div>';
}

function template_Shop_invGroup()
{
	global $context, $txt, $modSettings, $scripturl;

	if (isset($_REQUEST['success']))
		echo '<div class="infobox">', $txt['Shop_groupcredits_sent'], '</div>';
	
	echo '
	<div class="roundframe">
		<div class="windowbg">
			<form method="post" action="', $scripturl,'?action=admin;area=shopinventory;sa=groupcredits2">
				<dl class="settings">
					<dt>
						', $txt['Shop_membergroup'], ':
					</dt>
					<dd>';
						// Loop through all available membergroups
						foreach	($context['shop_usergroups'] as $group)
							echo '
							<input type="checkbox" name="usergroup[]" value="', $group['id'], '" />', $group['name'], '<br />';
					echo '
					</dd>
					<dt>
						', $txt['Shop_groupcredits_action'], '
					</dt>
					<dd>
						<label><input class="input_radio" type="radio" name="m_action" value="add" checked />', $txt['Shop_groupcredits_add'], '</label><br />
						<label><input class="input_radio" type="radio" name="m_action" value="sub" />', $txt['Shop_groupcredits_substract'], '</label>
					</dd>
					<dt>
						', $txt['Shop_bank_amount'], '
					</dt>
					<dd>
						', $modSettings['Shop_credits_prefix'], '<input class="input_text"  type="number" min="0" name="amount" id="amount" value="0" size="10" /> ', $modSettings['Shop_credits_suffix'], '
					</dd>
				</dl>
				<input class="button floatleft" type="submit" value="', $txt['go'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>
	</div>';
}

function template_Shop_invCredits()
{
	global $context, $txt, $modSettings, $scripturl;

	if (isset($_REQUEST['updated']))
		echo '<div class="infobox">', $txt['Shop_groupcredits_sent'], '</div>';
	
	echo '
	<div class="roundframe">
		<div class="windowbg">
			<form method="post" action="', $scripturl,'?action=admin;area=shopinventory;sa=usercredits2">
				<dl class="settings">
					<dt>
						', $txt['Shop_inventory_member_name'], '<br/>
						<span class="smalltext">', $txt['Shop_inventory_members_desc'], '</span>
					</dt>
					<dd>
						<input name="membername" id="membername" />
						<div id="membernameItemContainer"></div>
					</dd>
					<dt>
						', $txt['Shop_bank_amount'], '
					</dt>
					<dd>
						', $modSettings['Shop_credits_prefix'], '<input class="input_text"  type="number" min="0" name="amount" id="amount" value="0" size="10" /> ', $modSettings['Shop_credits_suffix'], '
					</dd>
				</dl>
				<input class="button floatleft" type="submit" value="', $txt['go'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>
	</div>
	<script>
		var oAddMemberSuggest = new smc_AutoSuggest({
			sSelf: \'oAddMemberSuggest\',
			sSessionId: \'', $context['session_id'], '\',
			sSessionVar: \'', $context['session_var'], '\',
			sSuggestId: \'to_suggest\',
			sControlId: \'membername\',
			sSearchType: \'member\',
			sPostName: \'memberid\',
			sURLMask: \'action=profile;u=%item_id%\',
			bItemList: true,
			sTextDeleteItem: \'', $txt['autosuggest_delete_item'], '\',
			sItemListContainerId: \'membernameItemContainer\'
		});
	</script>';
}

function template_Shop_invItems()
{
	global $context, $txt, $modSettings, $scripturl;

	if (isset($_REQUEST['updated']))
		echo '<div class="infobox">', $txt['Shop_useritems_sent'], '</div>';
	
	echo '
	<div class="roundframe">
		<div class="windowbg">
			<form method="post" action="', $scripturl,'?action=admin;area=shopinventory;sa=useritems2">
				<dl class="settings">
					<dt>
						', $txt['Shop_inventory_member_name'], '<br/>
						<span class="smalltext">', $txt['Shop_inventory_members_desc'], '</span>
					</dt>
					<dd>
						<input name="membername" id="membername" />
						<div id="membernameItemContainer"></div>
					</dd>
					<dt>
						', $txt['Shop_gift_item_select'], '
					</dt>
					<dd>';

				// Do we even have items?
				if (!empty($context['shop_items_list'])) {
					echo '
						<select name="item" id="item">
							<optgroup label="', $txt['Shop_gift_item_select'], '">';
							// List the categories
							foreach ($context['shop_items_list'] as $item)
							echo '
								<option value="', $item['id'], '">', $item['name'], '</option>';
						echo '
							</optgroup>
						</select>';
				}
				else
					echo '
						<strong>', $txt['Shop_inventory_useritems_noitems'], '</strong>';


			echo '
					</dd>
				</dl>
				<input class="button floatleft" type="submit" value="', $txt['go'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>
	</div>
	<script>
		var oAddMemberSuggest = new smc_AutoSuggest({
			sSelf: \'oAddMemberSuggest\',
			sSessionId: \'', $context['session_id'], '\',
			sSessionVar: \'', $context['session_var'], '\',
			sSuggestId: \'to_suggest\',
			sControlId: \'membername\',
			sSearchType: \'member\',
			sPostName: \'memberid\',
			sURLMask: \'action=profile;u=%item_id%\',
			bItemList: true,
			sTextDeleteItem: \'', $txt['autosuggest_delete_item'], '\',
			sItemListContainerId: \'membernameItemContainer\'
		});
	</script>';
}