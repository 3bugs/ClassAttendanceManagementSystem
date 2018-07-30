package example.com.classattendancemanagementsystem.fragment;

import android.content.Context;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.app.ActionBar;
import android.support.v7.app.AppCompatActivity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;

import example.com.classattendancemanagementsystem.R;

public class ScanQrCodeFragment extends Fragment {

    private ScanQrCodeFragmentListener mListener;

    public ScanQrCodeFragment() {
        // Required empty public constructor
    }

    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_scan_qr_code, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        if (getActivity() != null) {
            ActionBar actionBar = ((AppCompatActivity) getActivity()).getSupportActionBar();
            if (actionBar != null) {
                actionBar.setTitle(getResources().getString(R.string.title_scan_qr));
            }
        }

        Button scanQrButton = view.findViewById(R.id.scan_qr_code_button);
        scanQrButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (mListener != null) {
                    mListener.onClickScanQrCodeButton();
                }
            }
        });
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof ScanQrCodeFragmentListener) {
            mListener = (ScanQrCodeFragmentListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement ScanQrCodeFragmentListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    public interface ScanQrCodeFragmentListener {
        void onClickScanQrCodeButton();
    }
}
