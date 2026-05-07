import mongoose from "mongoose";

const FeedbackSchema = new mongoose.Schema(
    {
        userId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "User",
            required: true,
        },
        videoId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "Video",
            required: true,
        },
        parentId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "Feedback",
            default: null, // For replies
        },
        rating: {
            type: Number,
            min: 0,
            max: 5,
            default: 0, // 0 if it's just a comment without rating
        },
        content: {
            type: String,
            required: true,
            trim: true,
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

const FeedbackModel = mongoose.model("Feedback", FeedbackSchema, "feedbacks");
export default FeedbackModel;
