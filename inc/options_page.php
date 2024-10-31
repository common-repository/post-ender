<?php
/**
 * Post Ender Options Page
 */
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>Post Ender</h2>
	<p>
		This is the settings page for the <a href="http://wirelesswombat.com/post-ender/">Post Ender</a> plugin. 
		This plugin adds HTML code at the beginning or end of your posts. Just add the text you want (including HTML code) in the textarea below
		and check the appropriate settings.
	</p>
	
	<form method="post" action="options.php">

		<?php 
			// Setup and retrieve our option values so they're available for use in our form
			settings_fields('post_ender_options');
			$options = get_option('post_ender'); 			
		?>
	
		<table class="form-table" style="margin-top: 20px; padding-bottom: 10px; border: 1px dotted #bbb; border-width:1px 0;">
			<tr valign="top">
				<th scope="row">
					<h3 style="margin-top: 10px;">Text to be added to posts</h3>
					<p>
						The text entered in the textarea below will be added to the beginning or end of each post if the "Add to all posts" checkbox
						is checked. 
					</p>
					<p>	
						You can also add the text in an individual post by using the <code>[post_ender]</code> shortcode 
						in a post.
					</p>
				</th>
			</tr>
			<tr>
				<td>
					<input id="add_to_all" name="post_ender[add_to_all]" class="checkbox" type="checkbox" value="1" <?php if (isset($options['add_to_all'])) { checked('1', $options['add_to_all']); } ?> />
					<label for="add_to_all"> Add text to all posts</label>
				</td>
			</tr>
			<tr>
				<td>
					<input id="text_position_start" name="post_ender[text_position]" class="radio" type="radio" value="start" <?php if (isset($options['text_position'])) { checked('start', $options['text_position']); } ?> />
					<label for="text_position_start"> Add text to the <strong>beginning of posts</strong></label>
					<br />
					<input id="text_position_end" name="post_ender[text_position]" class="radio" type="radio" value="end" <?php if (isset($options['text_position'])) { checked('end', $options['text_position']); } ?> />
					<label for="text_position_end"> Add text to the <strong>end of posts</strong></label>
					<br />
					<input id="text_position_both" name="post_ender[text_position]" class="radio" type="radio" value="both" <?php if (isset($options['text_position'])) { checked('both', $options['text_position']); } ?> />
					<label for="text_position_both"> Add text to the <strong>beginning and end of posts</strong></label>
				</td>
			</tr>
			<tr>
				<td>
					<textarea class="mce_editor" name="post_ender[post_text]" style="width: 90%; height: 150px; padding: 10px;"><?php echo $options['post_text']; ?></textarea>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>

	</form>
</div>

