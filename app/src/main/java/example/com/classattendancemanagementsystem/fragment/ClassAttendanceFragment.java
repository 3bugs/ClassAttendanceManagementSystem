package example.com.classattendancemanagementsystem.fragment;

import android.content.Context;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;

import example.com.classattendancemanagementsystem.R;
import example.com.classattendancemanagementsystem.db.LocalDb;
import example.com.classattendancemanagementsystem.model.User;
import example.com.classattendancemanagementsystem.net.ApiClient;
import example.com.classattendancemanagementsystem.net.GetCourseResponse;
import example.com.classattendancemanagementsystem.net.MyRetrofitCallback;
import example.com.classattendancemanagementsystem.net.WebServices;
import retrofit2.Call;
import retrofit2.Retrofit;

public class ClassAttendanceFragment extends Fragment {

    private ClassAttendanceFragmentListener mListener;

    public ClassAttendanceFragment() {
        // Required empty public constructor
    }

    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_class_attendance, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        getCourseByStudent();
    }

    private void getCourseByStudent() {
        Retrofit retrofit = ApiClient.getClient();
        WebServices services = retrofit.create(WebServices.class);

        /*final ProgressDialog progressDialog = ProgressDialog.show(
                getActivity(),
                null,
                "กำลังส่งข้อมูลการเข้าเรียน...",
                true
        );*/

        ProgressBar progressBar = getView().findViewById(R.id.course_progress_bar);


        User user = new LocalDb(getContext()).getUser();
        Call<GetCourseResponse> call = services.getCourseByStudent(user.id);
        call.enqueue(new MyRetrofitCallback<>(
                getActivity(),
                null,
                progressBar,
                new MyRetrofitCallback.MyRetrofitCallbackListener<GetCourseResponse>() {
                    @Override
                    public void onSuccess(GetCourseResponse responseBody) {
                        /*String courseCode = responseBody.courseCode;
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
                        Utils.showOkDialog(getActivity(), msg);*/
                    }
                }
        ));
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof ClassAttendanceFragmentListener) {
            mListener = (ClassAttendanceFragmentListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement ClassAttendanceFragmentListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    public interface ClassAttendanceFragmentListener {
    }
}
