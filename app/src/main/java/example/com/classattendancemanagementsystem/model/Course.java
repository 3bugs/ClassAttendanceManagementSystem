package example.com.classattendancemanagementsystem.model;

import com.google.gson.annotations.SerializedName;

import java.util.Locale;

public class Course {

    @SerializedName("id")
    public int id;
    @SerializedName("code")
    public String code;
    @SerializedName("name")
    public String name;

    public Course(int id, String code, String name) {
        this.id = id;
        this.code = code;
        this.name = name;
    }

    @Override
    public String toString() {
        return String.format(Locale.getDefault(), "%s %s", code, name);
    }
}
