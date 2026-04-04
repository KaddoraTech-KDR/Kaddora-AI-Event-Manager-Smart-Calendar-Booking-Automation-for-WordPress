<div class="wrap kaem-settings">
  <h1 class="kaem-title">⚙️ Plugin Settings</h1>

  <form method="post" action="options.php" class="kaem-settings-form">
    <?php settings_fields('kaem_settings_group'); ?>

    <div class="kaem-settings-card">

      <table class="form-table kaem-form-table">

        <tr>
          <th>Enable AI</th>
          <td>
            <label class="kaem-switch">
              <input type="checkbox" name="kaem_enable_ai" value="1"
                <?php checked(1, get_option('kaem_enable_ai')); ?>>
              <span class="kaem-slider"></span>
            </label>
          </td>
        </tr>

        <tr>
          <th>Enable Booking</th>
          <td>
            <label class="kaem-switch">
              <input type="checkbox" name="kaem_enable_booking" value="1"
                <?php checked(1, get_option('kaem_enable_booking')); ?>>
              <span class="kaem-slider"></span>
            </label>
          </td>
        </tr>

        <tr>
          <th>Enable Pro Features</th>
          <td>
            <label class="kaem-switch">
              <input type="checkbox" name="kaem_pro_mode" value="1"
                <?php checked(1, get_option('kaem_pro_mode')); ?>>
              <span class="kaem-slider"></span>
            </label>
          </td>
        </tr>

        <tr>
          <th>License Key</th>
          <td>
            <input type="text" class="regular-text kaem-input"
              name="kaem_license_key"
              value="<?php echo esc_attr(get_option('kaem_license_key')); ?>">
          </td>
        </tr>

        <tr>
          <th>API Token</th>
          <td>
            <input type="text" class="regular-text kaem-input"
              name="kaem_api_token"
              value="<?php echo esc_attr(get_option('kaem_api_token')); ?>">
          </td>
        </tr>

      </table>

      <div class="kaem-submit">
        <?php submit_button('Save Settings'); ?>
      </div>

    </div>
  </form>
</div>