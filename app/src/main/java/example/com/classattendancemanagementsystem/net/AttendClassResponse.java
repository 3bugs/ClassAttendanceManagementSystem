package example.com.classattendancemanagementsystem.net;

import com.google.gson.annotations.SerializedName;

public class AttendClassResponse extends BaseResponse {

    @SerializedName("course_code")
    public String courseCode;
    @SerializedName("course_name")
    public String courseName;
    @SerializedName("class_number")
    public int classNumber;
    @SerializedName("class_date")
    public String classDate;
    @SerializedName("attend_date")
    public String attendDate;

}
