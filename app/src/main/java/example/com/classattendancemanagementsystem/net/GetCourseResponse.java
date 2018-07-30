package example.com.classattendancemanagementsystem.net;

import com.google.gson.annotations.SerializedName;

import java.util.List;

import example.com.classattendancemanagementsystem.model.Course;

public class GetCourseResponse extends BaseResponse {

    @SerializedName("course_list")
    public List<Course> courseList;

}
