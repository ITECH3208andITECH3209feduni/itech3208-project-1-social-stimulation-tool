import mongoose from "mongoose";

const LevelSchema = new mongoose.Schema(
    {
        name: {
            type: String,
            required: true,
            enum: ["Beginner", "Intermediate", "Advanced"],
            default: "Beginner",
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
