package example.com.classattendancemanagementsystem.net;

import com.google.gson.annotations.SerializedName;

import java.util.List;

import example.com.classattendancemanagementsystem.model.ClassAttendance;
import example.com.classattendancemanagementsystem.model.Course;

public class GetClassAttendanceResponse extends BaseResponse {

    @SerializedName("class_attendance_list")
    public List<ClassAttendance> classAttendanceList;

}
