import mongoose from "mongoose";

const FeedbackSchema = new mongoose.Schema(
    {
        userId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "User",
            required: true,
        },
        verifiedStatus: {
            type: Boolean,
        },
        rating: {
            type: Number,
            min: 1,
            max: 5,
            default: 1,
        },
        content: {
            type: String,
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
