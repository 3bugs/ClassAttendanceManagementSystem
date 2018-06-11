package example.com.classattendancemanagementsystem.net;

import android.app.Activity;
import android.app.ProgressDialog;
import android.util.Log;
import android.view.View;

import java.util.Locale;

import example.com.classattendancemanagementsystem.etc.Utils;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MyRetrofitCallback<T extends BaseResponse> implements Callback<T> {

    private static final String TAG = MyRetrofitCallback.class.getName();

    private Activity mActivity;
    private ProgressDialog mProgressDialog;
    private View mProgressView;
    private MyRetrofitCallbackListener<T> mListener;

    public MyRetrofitCallback(Activity activity,
                              ProgressDialog progressDialog,
                              View progressView,
                              MyRetrofitCallbackListener<T> listener) {
        this.mActivity = activity;
        this.mProgressDialog = progressDialog;
        this.mProgressView = progressView;
        mListener = listener;
    }

    @Override
    public void onResponse(Call<T> call, Response<T> response) {
        if (mProgressDialog != null) {
            mProgressDialog.dismiss();
        }
        if (mProgressView != null) {
            mProgressView.setVisibility(View.GONE);
        }

        if (response.isSuccessful()) {
            T responseBody = response.body();

            if (responseBody != null) {
                int errorCode = responseBody.errorCode;

                if (errorCode == 0) {
                    if (mListener != null) {
                        mListener.onSuccess(responseBody);
                    }
                } else {
                    String msg = responseBody.errorMessage;
                    Utils.showOkDialog(mActivity, msg);

                    String logMsg = String.format(
                            Locale.getDefault(),
                            "%s [%s]",
                            msg, responseBody.errorMessageMore
                    );
                    Log.d(TAG, logMsg);
                }
            } else {
                String msg = "Network error!";
                Utils.showOkDialog(mActivity, msg);
            }
        } else { // HTTP request failed
            String msg = String.format(
                    Locale.getDefault(),
                    "HTTP request failed! HTTP status code: %d [%s]",
                    response.code(), response.message()
            );
            Utils.showOkDialog(mActivity, msg);
            Log.e(TAG, msg);
        }
    }

    @Override
    public void onFailure(Call<T> call, Throwable t) {
        if (mProgressDialog != null) {
            mProgressDialog.dismiss();
        }
        if (mProgressView != null) {
            mProgressView.setVisibility(View.GONE);
        }

        String msg = "ไม่สามารถเชื่อมต่อเครือข่ายได้: " + t.getMessage();
        Utils.showOkDialog(mActivity, msg);
    }

    public interface MyRetrofitCallbackListener<T extends BaseResponse> {
        void onSuccess(T responseBody);
    }
}
