package example.com.classattendancemanagementsystem;

import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.support.design.widget.NavigationView;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

import java.util.Locale;

import example.com.classattendancemanagementsystem.db.LocalDb;
import example.com.classattendancemanagementsystem.etc.Utils;
import example.com.classattendancemanagementsystem.model.User;
import example.com.classattendancemanagementsystem.net.ApiClient;
import example.com.classattendancemanagementsystem.net.AttendClassResponse;
import example.com.classattendancemanagementsystem.net.MyRetrofitCallback;
import example.com.classattendancemanagementsystem.net.WebServices;
import retrofit2.Call;
import retrofit2.Retrofit;

import static example.com.classattendancemanagementsystem.QrScanActivity.KEY_QR_CODE_TEXT;

public class MainActivity extends AppCompatActivity
        implements NavigationView.OnNavigationItemSelectedListener {

    private static final String TAG = MainActivity.class.getName();
    private static final int REQUEST_SCAN_QR_CODE = 1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        setupToolbarAndDrawer();

        Button scanQrButton = findViewById(R.id.scan_qr_code_button);
        scanQrButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent intent = new Intent(MainActivity.this, QrScanActivity.class);
                startActivityForResult(intent, REQUEST_SCAN_QR_CODE);
            }
        });
    }

    private void setupToolbarAndDrawer() {
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

        DrawerLayout drawer = findViewById(R.id.drawer_layout);
        ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(
                this, drawer, toolbar, R.string.navigation_drawer_open, R.string.navigation_drawer_close);
        drawer.addDrawerListener(toggle);
        toggle.syncState();

        NavigationView navigationView = findViewById(R.id.nav_view);
        navigationView.setNavigationItemSelectedListener(this);

        View headerView = navigationView.getHeaderView(0);
        TextView displayNameTextView = headerView.findViewById(R.id.display_name_text_view);
        TextView usernameTextView = headerView.findViewById(R.id.username_text_view);

        User user = new LocalDb(this).getUser();
        displayNameTextView.setText(user.displayName);
        usernameTextView.setText(user.username);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == REQUEST_SCAN_QR_CODE) {
            if (resultCode == RESULT_OK) {
                String qrText = data.getStringExtra(KEY_QR_CODE_TEXT);
                Utils.showLongToast(MainActivity.this, "Class ID: " + qrText);

                doAttendClass(Integer.parseInt(qrText));
            }
        }
    }

    private void doAttendClass(int classId) {
        Retrofit retrofit = ApiClient.getClient();
        WebServices services = retrofit.create(WebServices.class);

        final ProgressDialog progressDialog = ProgressDialog.show(
                this,
                null,
                "กำลังส่งข้อมูลการเข้าเรียน...",
                true
        );

        User user = new LocalDb(this).getUser();
        Call<AttendClassResponse> call = services.attendClass(classId, user.id);
        call.enqueue(new MyRetrofitCallback<>(
                MainActivity.this,
                progressDialog,
                null,
                new MyRetrofitCallback.MyRetrofitCallbackListener<AttendClassResponse>() {
                    @Override
                    public void onSuccess(AttendClassResponse responseBody) {
                        String courseCode = responseBody.courseCode;
                        String courseName = responseBody.courseName;
                        int classNumber = responseBody.classNumber;
                        String classDate = responseBody.classDate;
                        String attendDate = responseBody.attendDate;

                        String msg = "บันทึกข้อมูลการเข้าเรียนสำเร็จ\n----------\n";
                        msg += String.format(
                                Locale.getDefault(),
                                "รหัสวิชา: %s\nชื่อวิชา: %s\nเรียนครั้งที่: %d\nวัน/เวลาที่เข้าเรียน: %s",
                                courseCode, courseName, classNumber, attendDate
                        );
                        Utils.showOkDialog(MainActivity.this, msg);
                    }
                }
        ));
    }

    @Override
    public void onBackPressed() {
        DrawerLayout drawer = findViewById(R.id.drawer_layout);
        if (drawer.isDrawerOpen(GravityCompat.START)) {
            drawer.closeDrawer(GravityCompat.START);
        } else {
            super.onBackPressed();
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();

        if (id == R.id.action_settings) {
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    @SuppressWarnings("StatementWithEmptyBody")
    @Override
    public boolean onNavigationItemSelected(MenuItem item) {
        int id = item.getItemId();

        if (id == R.id.nav_course) {
            // Handle the camera action
        } else if (id == R.id.nav_attendance) {

        } else if (id == R.id.nav_logout) {
            logout();
        }

        DrawerLayout drawer = findViewById(R.id.drawer_layout);
        drawer.closeDrawer(GravityCompat.START);
        return true;
    }

    private void logout() {
        new AlertDialog.Builder(this)
                .setTitle("ยืนยันออกจากระบบ")
                .setMessage("ต้องการออกจากระบบ ใช่หรือไม่?")
                .setPositiveButton("ออกจากระบบ", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialogInterface, int i) {
                        new LocalDb(MainActivity.this).logoutUser();
                        Intent intent = new Intent(
                                MainActivity.this, LoginActivity.class
                        );
                        startActivity(intent);
                        finish();
                    }
                })
                .setNegativeButton("ยกเลิก", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialogInterface, int i) {
                    }
                })
                .show();
    }
}
