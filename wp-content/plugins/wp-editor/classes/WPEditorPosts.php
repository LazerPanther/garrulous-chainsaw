<?php
class WPEditorPosts {
  
  public static function addPostsJquery($editor) {
    global $post;
    if(WPEditorSetting::getValue('enable_post_editor')) {
      $theme = WPEditorSetting::getValue('post_editor_theme') ? WPEditorSetting::getValue('post_editor_theme') : 'default';
      $activeLine = WPEditorSetting::getValue('enable_post_active_line') == 1 ? 'activeline-' . $theme : false;
      $post_editor_settings = array(
        'mode' => 'text/html',
        'theme' => $theme,
        'activeLine' => $activeLine,
        'lineNumbers' => WPEditorSetting::getValue('enable_post_line_numbers') == 1 ? true : false,
        'lineWrapping' => WPEditorSetting::getValue('enable_post_line_wrapping') == 1 ? true : false,
        'enterImgUrl' => __('Enter the URL of the image:', 'wpeditor'),
        'enterImgDescription' => __('Enter a description of the image:', 'wpeditor'),
        'lookupWord' => __('Enter a word to look up:', 'wpeditor'),
        'tabSize' => WPEditorSetting::getValue('enable_post_tab_size') ? WPEditorSetting::getValue('enable_post_tab_size') : 4,
        'indentWithTabs' => WPEditorSetting::getValue('enable_post_tab_size') == 'tabs' ? true : false,
        'indentUnit' => WPEditorSetting::getValue('post_indent_unit') == '' ? 2 : WPEditorSetting::getValue('post_indent_unit'),
        'editorHeight' => WPEditorSetting::getValue('enable_post_editor_height') ? WPEditorSetting::getValue('enable_post_editor_height') : false,
        'fontSize' => WPEditorSetting::getValue("change_post_editor_font_size") ? WPEditorSetting::getValue("change_post_editor_font_size") . "px" : "12px",
        'save' => isset($post->post_status) && $post->post_status == 'publish' ? __('Update', 'wpeditor') : __('Save', 'wpeditor')
      );
      WPEditorAdmin::editorStylesheetAndScripts();
      wp_enqueue_script('wp-editor-posts-jquery');
      wp_localize_script('wp-editor-posts-jquery', 'WPEPosts', $post_editor_settings);
    }
    return $editor;
  }
  
}