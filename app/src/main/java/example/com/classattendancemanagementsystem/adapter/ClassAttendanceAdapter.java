package example.com.classattendancemanagementsystem.adapter;

import android.content.Context;
import android.graphics.Color;
import android.support.annotation.NonNull;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import java.util.List;

import example.com.classattendancemanagementsystem.R;
import example.com.classattendancemanagementsystem.model.ClassAttendance;

public class ClassAttendanceAdapter extends ArrayAdapter<ClassAttendance> {

    private Context mContext;
    private int mItemResId;
    private List<ClassAttendance> mClassAttendanceList;

    public ClassAttendanceAdapter(@NonNull Context context, int resource, @NonNull List<ClassAttendance> classAttendanceList) {
        super(context, resource, classAttendanceList);

        this.mContext = context;
        this.mItemResId = resource;
        this.mClassAttendanceList = classAttendanceList;
    }

    @NonNull
    @Override
    public View getView(int position, View convertView, @NonNull ViewGroup parent) {
        View view = convertView;

        if (view == null) {
            LayoutInflater inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            if (inflater != null) {
                view = inflater.inflate(mItemResId, null);
            }
        }

        view.setBackgroundColor(Color.TRANSPARENT);

        ClassAttendance ca = getItem(position);
        if (ca != null) {
            TextView classNumberTextView = view.findViewById(R.id.class_number_text_view);
            TextView attendDateTextView = view.findViewById(R.id.attend_date_text_view);
            TextView attendTimeTextView = view.findViewById(R.id.attend_time_text_view);

            classNumberTextView.setText(String.valueOf(ca.classNumber));
            attendDateTextView.setText(ca.attendDateFormat != null ? ca.attendDateFormat : "ขาดเรียน");
            attendTimeTextView.setText(ca.attendTimeFormat != null ? ca.attendTimeFormat + " น." : "");

            if (ca.attendDate != null) {
                if (ca.dateDiffMinutes > 15) {
                    view.setBackgroundColor(mContext.getResources().getColor(R.color.late));
                }
            } else {
                view.setBackgroundColor(mContext.getResources().getColor(R.color.absent));
            }
        }

        return view;
    }
}
