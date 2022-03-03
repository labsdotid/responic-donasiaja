<?php

namespace Responic_Donasiaja;


class Admin
{
    public function menu()
    {
        add_menu_page(
            __('Responic'),
            __('Responic'),
            'manage_options',
            'responic-donaisaja',
            [$this, 'page'],
            'dashicons-schedule',
            2
        );
    }

    public function on_save()
    {
        if (isset($_POST['action']) && isset($_POST['_wpnonce'])) :
            if (wp_verify_nonce($_POST['_wpnonce'], 'responic-donasiaja')) :

                unset($_POST['save']);
                unset($_POST['_wpnonce']);
                unset($_POST['_wp_http_referer']);

                if ($_POST['action'] == 'reset') {
                    Setting::load()->reset();
                } else {
                    if (isset($_POST['api_url'])) {

                        Setting::load()->update('wanotif_url', $_POST['api_url']);
                    }

                    if (isset($_POST['api_key'])) {
                        Setting::load()->update('wanotif_apikey', $_POST['api_key']);
                    }
                }

                \flush_rewrite_rules();

                add_action('admin_notices', function () {
                    echo '<div id="message" class="updated notice notice-success"><p><strong>' . __('Your settings have been saved.', 'salesloo') . '</strong></p></div>';
                });
            endif;
        endif;
    }

    public function page()
    {

        $api_url = site_url() . '/wp-json/responic-donasiaja';
?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Responic Settings</h1>
            <hr class="wp-header-end">
            <form name="post" action="" method="post" class="salesloo-form">
                <?php wp_nonce_field('responic-donasiaja'); ?>
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content" style="position: relative;">
                            <table class="form-table" role="presentation">
                                <tbody>

                                    <tr>
                                        <th scope="row"><label for="mda-api-url">API URl</label></th>
                                        <td>
                                            <input class="regular-text" type="text" id="api_url" name="api_url" placeholder="Api URL" value="<?php echo $api_url; ?>" readonly>
                                            <p class="description">Ubah api wanotif menjadi api dari responic</p>
                                            <p class="description"><i>Api Wanotif saat ini : <?php echo Setting::load()->wanotif_url; ?></i></p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th scope="row"><label for="mda-api-key">API Key</label></th>
                                        <td>
                                            <input class="regular-text" type="text" id="api_key" name="api_key" placeholder="Api key" value="<?php echo Setting::load()->wanotif_apikey; ?>">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p>
                                <button class="button button-primary" type="submit" name="action" value="save">Save</button>
                                <button class="button" type="submit" name="action" value="reset">Reset</button>
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
<?php
    }
}
