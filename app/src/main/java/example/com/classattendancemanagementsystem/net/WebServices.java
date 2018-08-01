package example.com.classattendancemanagementsystem.net;

import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;

public interface WebServices {

    @FormUrlEncoded
    @POST("ldap.php")
    Call<LoginResponse> login(
            @Field("username") String username,
            @Field("password") String password
    );

    @FormUrlEncoded
    @POST("api.php/insert_student")
    Call<LoginResponse> addUser(
            @Field("username") String username,
            @Field("display_name") String displayName
    );

    @FormUrlEncoded
    @POST("api.php/insert_class_attendance")
    Call<AttendClassResponse> attendClass(
            @Field("class_id") int classId,
            @Field("student_id") int userId
    );

    @FormUrlEncoded
    @POST("api.php/select_course_by_student")
    Call<GetCourseResponse> getCourseByStudent(
            @Field("student_id") int userId
    );

    @FormUrlEncoded
    @POST("api.php/select_class_attendance")
    Call<GetClassAttendanceResponse> getClassAttendance(
            @Field("course_id") int courseId,
            @Field("student_id") int studentId
    );
}
