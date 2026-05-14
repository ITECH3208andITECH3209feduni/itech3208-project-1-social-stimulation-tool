import mongoose from "mongoose";

const VideoSchema = new mongoose.Schema(
    {
        // MARK: - BASIC INFO
        title: {
            type: String,
            required: true,
            trim: true,
        },
        slug: {
            type: String,
            unique: true,
            lowercase: true,
            trim: true,
        },
        description: {
            type: String,
            default: "",
            trim: true,
        },

        // MARK: - MEDIA
        video: {
            url: {
                type: String,
                default: "",
            },
            cloudinaryId: {
                type: String,
                default: "",
            },
        },
        thumbnail: {
            url: {
                type: String,
                default: "",
            },
            cloudinaryId: {
                type: String,
                default: "",
            },
        },

        // MARK: - RELATIONS
        categoryId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "Category",
            required: true,
        },
        levelId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "Level",
            required: true,
        },
        uploadedBy: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "User",
            required: true,
        },

        // MARK: - METADATA
        tags: {
            type: [String],
            default: [],
        },
        duration: {
            type: Number, // Unit: seconds
            default: 0,
        },
        views: {
            type: Number,
            default: 0,
        },

        // MARK: - STATUS
        status: {
            type: String,
            enum: ["draft", "published", "archived"],
            default: "draft",
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

// Auto-generate slug from title before saving
VideoSchema.pre("save", async function () {
    if (this.isModified("title")) {
        this.slug = this.title
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, "")
            .replace(/\s+/g, "-")
            .replace(/-+/g, "-");
    }
});

const VideoModel = mongoose.model("Video", VideoSchema, "videos");
export default VideoModel;
