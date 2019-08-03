package example.com.classattendancemanagementsystem.fragment;

import android.content.Context;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import example.com.classattendancemanagementsystem.R;
import example.com.classattendancemanagementsystem.db.LocalDb;
import example.com.classattendancemanagementsystem.model.User;

public class UserProfileFragment extends Fragment {

    private UserProfileFragmentListener mListener;

    public UserProfileFragment() {
        // Required empty public constructor
    }

    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_user_profile, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        //ImageView profileImageView = view.findViewById(R.id.profile_image_view);
        TextView displayNameTextView = view.findViewById(R.id.display_name_text_view);
        TextView idTextView = view.findViewById(R.id.id_text_view);

        User user = new LocalDb(getActivity()).getUser();
        displayNameTextView.setText(user.displayName);
        idTextView.setText(String.valueOf(user.username));
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof UserProfileFragmentListener) {
            mListener = (UserProfileFragmentListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement UserProfileFragmentListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    public interface UserProfileFragmentListener {
    }
}
