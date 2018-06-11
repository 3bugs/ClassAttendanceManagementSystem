package example.com.classattendancemanagementsystem.net;

import com.google.gson.annotations.SerializedName;

import example.com.classattendancemanagementsystem.model.User;

public class LoginResponse extends BaseResponse {

    @SerializedName("login_success")
    public int loginSuccess;
    @SerializedName("user")
    public User user;

}
