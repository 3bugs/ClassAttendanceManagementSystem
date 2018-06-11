package example.com.classattendancemanagementsystem;

import android.app.ProgressDialog;
import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import example.com.classattendancemanagementsystem.db.LocalDb;
import example.com.classattendancemanagementsystem.etc.Utils;
import example.com.classattendancemanagementsystem.model.User;
import example.com.classattendancemanagementsystem.net.ApiClient;
import example.com.classattendancemanagementsystem.net.LoginResponse;
import example.com.classattendancemanagementsystem.net.MyRetrofitCallback;
import example.com.classattendancemanagementsystem.net.WebServices;
import retrofit2.Call;
import retrofit2.Retrofit;

public class LoginActivity extends AppCompatActivity implements View.OnClickListener {

    private String mUsername;
    private String mPassword;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        User user = new LocalDb(this).getUser();
        if (user != null) {
            startMainActivity();
            finish();
            return;
        }

        setContentView(R.layout.activity_login);

        Button loginButton = findViewById(R.id.login_button);
        loginButton.setOnClickListener(this);
    }

    @Override
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.login_button:
                if (validateInput()) {
                    Utils.hideKeyboard(this);
                    doLogin();
                } else {
                    Toast.makeText(this, "กรุณากรอกข้อมูลให้ครบถ้วน", Toast.LENGTH_LONG).show();
                }
                break;
        }
    }

    private boolean validateInput() {
        EditText usernameEditText = findViewById(R.id.username_edit_text);
        EditText passwordEditText = findViewById(R.id.password_edit_text);

        mUsername = usernameEditText.getText().toString().trim();
        mPassword = passwordEditText.getText().toString().trim();

        boolean valid = true;

        if (mUsername.equals("")) {
            usernameEditText.setError("กรอกชื่อผู้ใช้งาน");
            valid = false;
        }
        if (mPassword.equals("")) {
            passwordEditText.setError("กรอกรหัสผ่าน");
            valid = false;
        }

        return valid;
    }

    private void doLogin() {
        Retrofit retrofit = ApiClient.getClient();
        WebServices services = retrofit.create(WebServices.class);

        Call<LoginResponse> call = services.login(mUsername, mPassword);
        final ProgressDialog progressDialog = ProgressDialog.show(
                this,
                null,
                "กำลังตรวจสอบข้อมูล...",
                true
        );
        call.enqueue(new MyRetrofitCallback<>(
                LoginActivity.this,
                progressDialog,
                null,
                new MyRetrofitCallback.MyRetrofitCallbackListener<LoginResponse>() {
                    @Override
                    public void onSuccess(LoginResponse responseBody) {
                        User user = responseBody.user;
                        //String msg = "เข้าสู่ระบบสำเร็จ\n\nยินดีต้อนรับคุณ" + user.toString();
                        //Utils.showLongToast(LoginActivity.this, msg);
                        doAddUser(user.username, user.displayName);
                    }
                }
        ));
    }

    private void doAddUser(String username, String displayName) {
        Retrofit retrofit = ApiClient.getClient();
        WebServices services = retrofit.create(WebServices.class);

        Call<LoginResponse> call = services.addUser(username, displayName);
        call.enqueue(new MyRetrofitCallback<>(
                LoginActivity.this,
                null,
                null,
                new MyRetrofitCallback.MyRetrofitCallbackListener<LoginResponse>() {
                    @Override
                    public void onSuccess(LoginResponse responseBody) {
                        User user = responseBody.user;
                        new LocalDb(LoginActivity.this).loginUser(user);
                        startMainActivity();
                    }
                }
        ));
    }

    private void startMainActivity() {
        Intent intent = new Intent(this, MainActivity.class);
        startActivity(intent);
        finish();
    }
}
