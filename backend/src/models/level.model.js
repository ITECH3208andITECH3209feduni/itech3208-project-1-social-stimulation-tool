import mongoose from "mongoose";

const LevelSchema = new mongoose.Schema(
    {
        name: {
            type: String,
            required: true,
            enum: ["beginner", "intermediate", "advanced"],
            default: "beginner",
        },
    },
    {
        timestamps: true,
        createdAt: "createdAt",
        updatedAt: "updatedAt",
    },
);

const LevelModel = mongoose.model("Level", LevelSchema, "levels");
export default LevelModel;
