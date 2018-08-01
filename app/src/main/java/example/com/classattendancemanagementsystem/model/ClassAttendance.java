package example.com.classattendancemanagementsystem.model;

import com.google.gson.annotations.SerializedName;

public class ClassAttendance {

    @SerializedName("class_number")
    public int classNumber;
    @SerializedName("class_date")
    public String classDate;
    @SerializedName("attend_date")
    public String attendDate;
    @SerializedName("attend_date_format")
    public String attendDateFormat;
    @SerializedName("attend_time_format")
    public String attendTimeFormat;
    @SerializedName("date_diff_minutes")
    public int dateDiffMinutes;

}
