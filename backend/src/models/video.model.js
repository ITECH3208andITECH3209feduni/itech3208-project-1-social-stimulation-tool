import mongoose from "mongoose";

const VideoSchema = new mongoose.Schema(
    {
        categoryId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "Category",
            required: true,
        },
        levelId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "Level",
            requried: true,
        },
        views: {
            type: Number,
            default: 0,
        },
        thumnail: {
            url: {
                type: String,
            },
            cloudinaryId: {
                type: String,
            },
        },
        description: {
            type: String,
        },
        duration: {
            type: Number, // Unit: seconds
        },
    },
    {
        timestamps: true,
        createdAt: "createdAt",
        updatedAt: "updatedAt",
    },
);

const VideoModel = mongoose.model("Video", VideoSchema, "videos");
export default VideoModel;
