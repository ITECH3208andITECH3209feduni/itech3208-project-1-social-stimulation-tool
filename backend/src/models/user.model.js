import mongoose from "mongoose";

const UserSchema = new mongoose.Schema(
    {
        firstName: {
            type: String,
            default: "",
        },
        lastName: {
            type: String,
            default: "",
        },
        username: {
            type: String,
            required: true,
            unique: true,
        },
        password: {
            type: String,
            required: true,
        },
        email: {
            type: String,
            required: true,
            unique: true,
        },
        avatar: {
            url: {
                type: String,
            },
            cloudinaryId: {
                type: String,
            },
        },
        location: {
            type: String,
        },
        acceptedTerms: {
            type: Boolean,
        },
        feedbackMeta: {
            // the time to show popup recently
            lastPromptAt: {
                type: Date,
            },
            // the time that user click "Maybe later" recently
            lastDismissedAt: {
                type: Date,
            },
            // the time that user sent feedback recently
            lastSubmittedAt: {
                type: Date,
            },
        },
        role: {
            type: String,
            enum: ["admin", "individual", "organization"],
            default: "individual",
        },
        accountStatus: {
            type: String,
            enum: ["active", "inactive", "suspended", "banned"],
            default: "active",
        },
        isDeleted: {
            type: Boolean,
            default: false,
        },
    },
    {
        timestamps: true,
        createdAt: "createdAt",
        updatedAt: "updatedAt",
    },
);

const UserModel = mongoose.model("User", UserSchema, "users");
export default UserModel;
