<?php
function lr_user_meta_form_field_phone_number($user)
{
    ?>
    <h3>اطلاعات تکمیلی</h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="user-phone">شماره موبایل</label>
            </th>
            <td>
                <input type="text"
                       class="regular-text ltr"
                       id="user-phone"
                       name="user_phone"
                       value="<?php echo get_user_meta($user->ID, '_lr_user_phone', true) ?>"
                       title="شماره موبایل خود را وارد کنید"
                >
                <p class="description">
                    شماره موبایل خود را وارد کنید
                </p>
            </td>
        </tr>
    </table>

    <?php
}

function lr_user_meta_form_field_phone_number_update($user_id)
{
    update_user_meta($user_id, '_lr_user_phone', $_POST['user_phone']);
}


// Add the field to user's own profile editing screen.
add_action('show_user_profile', 'lr_user_meta_form_field_phone_number');
// Add the field to user profile editing screen.
add_action('edit_user_profile', 'lr_user_meta_form_field_phone_number');
// Add the save action to user's own profile editing screen update.
add_action('personal_options_update', 'lr_user_meta_form_field_phone_number_update');
// Add the save action to user profile editing screen update.
add_action('edit_user_profile_update', 'lr_user_meta_form_field_phone_number_update');