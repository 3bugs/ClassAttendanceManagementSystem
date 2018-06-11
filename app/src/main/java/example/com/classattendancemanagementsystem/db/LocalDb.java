package example.com.classattendancemanagementsystem.db;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import example.com.classattendancemanagementsystem.model.User;

public class LocalDb {

    private static final String TAG = LocalDb.class.getName();
    public static final String STORED_DATE_FORMAT = "yyyy-MM-dd";

    private static final String DATABASE_NAME = "smart_pick.db";
    private static final int DATABASE_VERSION = 1;

    // เทเบิล user
    // +-----+----+----------+--------------+
    // | _id | id | username | display_name |
    // +-----+----+----------+--------------+
    // |     |    |          |              |

    private static final String TABLE_USER = "user";
    private static final String COL_ID = "_id";
    private static final String COL_USER_ID = "user_id";
    private static final String COL_USERNAME = "username";
    private static final String COL_DISPLAY_NAME = "display_name";

    private static final String SQL_CREATE_TABLE_USER =
            "CREATE TABLE " + TABLE_USER + "("
                    + COL_ID + " INTEGER PRIMARY KEY AUTOINCREMENT, "
                    + COL_USER_ID + " INTEGER, "
                    + COL_USERNAME + " TEXT, "
                    + COL_DISPLAY_NAME + " TEXT "
                    + ")";

    private static DatabaseHelper sDbHelper;
    private SQLiteDatabase mDatabase;
    private Context mContext;

    public LocalDb(Context context) {
        mContext = context.getApplicationContext();

        if (sDbHelper == null) {
            sDbHelper = new DatabaseHelper(context);
        }
        mDatabase = sDbHelper.getWritableDatabase();
    }

    public boolean loginUser(User user) {
        logoutUser();
        ContentValues cv = new ContentValues();
        cv.put(COL_USER_ID, user.id);
        cv.put(COL_USERNAME, user.username);
        cv.put(COL_DISPLAY_NAME, user.displayName);
        return mDatabase.insert(TABLE_USER, null, cv) != -1;
    }

    public int logoutUser() {
        return mDatabase.delete(TABLE_USER, null, null);
    }

    public User getUser() {
        Cursor cursor = mDatabase.query(
                TABLE_USER,
                null,
                null,
                null,
                null,
                null,
                null
        );
        if (cursor.getCount() == 0) {
            cursor.close();
            return null;
        } else {
            cursor.moveToFirst();
            User user = new User(
                    cursor.getInt(cursor.getColumnIndex(COL_USER_ID)),
                    cursor.getString(cursor.getColumnIndex(COL_USERNAME)),
                    cursor.getString(cursor.getColumnIndex(COL_DISPLAY_NAME))
            );
            cursor.close();
            return user;
        }
    }

    private static class DatabaseHelper extends SQLiteOpenHelper {

        DatabaseHelper(Context context) {
            super(context, DATABASE_NAME, null, DATABASE_VERSION);
        }

        @Override
        public void onCreate(SQLiteDatabase db) {
            db.execSQL(SQL_CREATE_TABLE_USER);
        }

        @Override
        public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
            db.execSQL("DROP TABLE IF EXISTS " + TABLE_USER);
            onCreate(db);
        }
    }
}
