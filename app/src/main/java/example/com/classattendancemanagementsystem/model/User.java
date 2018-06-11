package example.com.classattendancemanagementsystem.model;

import com.google.gson.annotations.SerializedName;

public class User {

    @SerializedName("id")
    public int id;
    @SerializedName("username")
    public String username;
    @SerializedName("display_name")
    public String displayName;

    public User(int id, String username, String displayName) {
        this.id = id;
        this.username = username;
        this.displayName = displayName;
    }

    @Override
    public String toString() {
        /*return String.format(
                Locale.getDefault(),
                "%s\nusername: %s",
                displayName, username
        );*/
        return displayName;
    }
}
