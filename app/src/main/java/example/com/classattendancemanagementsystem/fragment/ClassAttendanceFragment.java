package example.com.classattendancemanagementsystem.fragment;

import android.content.Context;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.app.ActionBar;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.Spinner;

import java.util.List;
import java.util.Locale;

import example.com.classattendancemanagementsystem.R;
import example.com.classattendancemanagementsystem.adapter.ClassAttendanceAdapter;
import example.com.classattendancemanagementsystem.adapter.SpinnerWithHintArrayAdapter;
import example.com.classattendancemanagementsystem.db.LocalDb;
import example.com.classattendancemanagementsystem.etc.Utils;
import example.com.classattendancemanagementsystem.model.ClassAttendance;
import example.com.classattendancemanagementsystem.model.Course;
import example.com.classattendancemanagementsystem.model.User;
import example.com.classattendancemanagementsystem.net.ApiClient;
import example.com.classattendancemanagementsystem.net.GetClassAttendanceResponse;
import example.com.classattendancemanagementsystem.net.GetCourseResponse;
import example.com.classattendancemanagementsystem.net.MyRetrofitCallback;
import example.com.classattendancemanagementsystem.net.WebServices;
import retrofit2.Call;
import retrofit2.Retrofit;

public class ClassAttendanceFragment extends Fragment {

    private static final String TAG = ClassAttendanceFragment.class.getName();

    private ProgressBar mProgressBar;
    private ListView mListView;
    private List<Course> mCourseList;
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

        if (getActivity() != null) {
            ActionBar actionBar = ((AppCompatActivity) getActivity()).getSupportActionBar();
            if (actionBar != null) {
                actionBar.setTitle(getResources().getString(R.string.title_class_attendance));
            }
        }

        mProgressBar = view.findViewById(R.id.course_progress_bar);
        mProgressBar.setVisibility(View.GONE);
        mListView = view.findViewById(R.id.class_attendance_list_view);

        if (mCourseList == null) {
            doGetCourseByStudent(view);
        } else {
            setupCourseSpinner(view);
        }
    }

    private void doGetCourseByStudent(final View view) {
        mProgressBar.setVisibility(View.VISIBLE);

        Retrofit retrofit = ApiClient.getClient();
        WebServices services = retrofit.create(WebServices.class);

        User user = new LocalDb(getContext()).getUser();
        Call<GetCourseResponse> call = services.getCourseByStudent(user.id);
        call.enqueue(new MyRetrofitCallback<>(
                getActivity(),
                null,
                mProgressBar,
                new MyRetrofitCallback.MyRetrofitCallbackListener<GetCourseResponse>() {
                    @Override
                    public void onSuccess(GetCourseResponse responseBody) {
                        mCourseList = responseBody.courseList;
                        setupCourseSpinner(view);

                        for (Course course : mCourseList) {
                            String msg = String.format(
                                    Locale.getDefault(),
                                    "ID: %d, Code: %s, Name: %s",
                                    course.id, course.code, course.name
                            );
                            Log.i(TAG, msg);
                        }
                    }
                }
        ));
    }

    private void setupCourseSpinner(View view) {
        Spinner courseSpinner = view.findViewById(R.id.course_spinner);
        mCourseList.add(new Course(0, "เลือกวิชา", ""));
        final SpinnerWithHintArrayAdapter<Course> adapter = new SpinnerWithHintArrayAdapter<>(
                getActivity(),
                android.R.layout.simple_spinner_dropdown_item,
                mCourseList
        );
        courseSpinner.setAdapter(adapter);
        courseSpinner.setSelection(adapter.getCount());

        courseSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int position, long id) {
                Course course = adapter.getItem(position);
                if (course != null && course.id > 0) {
                    doGetClassAttendance(course.id);
                }
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
    }

    private void doGetClassAttendance(int courseId) {
        if (getContext() == null) return;

        Retrofit retrofit = ApiClient.getClient();
        WebServices services = retrofit.create(WebServices.class);

        User user = new LocalDb(getContext()).getUser();
        Call<GetClassAttendanceResponse> call = services.getClassAttendance(courseId, user.id);
        call.enqueue(new MyRetrofitCallback<>(
                getActivity(),
                null,
                null,
                new MyRetrofitCallback.MyRetrofitCallbackListener<GetClassAttendanceResponse>() {
                    @Override
                    public void onSuccess(GetClassAttendanceResponse responseBody) {
                        List<ClassAttendance> classAttendancesList = responseBody.classAttendanceList;
                        ClassAttendanceAdapter adapter = new ClassAttendanceAdapter(
                                getContext(),
                                R.layout.item_class_attendance,
                                classAttendancesList
                        );
                        mListView.setAdapter(adapter);
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
