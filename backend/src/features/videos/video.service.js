import UPLOAD_FOLDERS from "#constants/upload-folder.constant.js";
import { VideoModel } from "#models/index.js";
import { cloudinaryUtil } from "#utils/index.js";
import VideoMessages from "./video.message.js";

const VideoService = {
    // MARK: - UPLOAD VIDEO FILE
    uploadVideo: async ({ videoId, file }) => {
        const existedVideo = await VideoModel.findById(videoId);

        if (!existedVideo) {
            throw VideoMessages.error.VIDEO_NOT_FOUND();
        }

        // Delete old video on Cloudinary if exists
        if (existedVideo.video?.cloudinaryId) {
            await cloudinaryUtil.deleteFile(existedVideo.video.cloudinaryId, "video");
        }

        const fileName = cloudinaryUtil.genFileName({
            prefix: "video",
            entityId: existedVideo._id,
        });

        const uploadedVideo = await cloudinaryUtil.uploadVideo({
            fileBuffer: file.buffer,
            folder: UPLOAD_FOLDERS.VIDEOS.CLIPS,
            fileName: fileName,
        });

        existedVideo.video = {
            url: uploadedVideo.secure_url,
            cloudinaryId: uploadedVideo.public_id,
        };

        // Auto-set duration from Cloudinary metadata
        if (uploadedVideo.duration) {
            existedVideo.duration = Math.round(uploadedVideo.duration);
        }

        await existedVideo.save();

        return {
            id: existedVideo._id,
            title: existedVideo.title,
            video: existedVideo.video,
            duration: existedVideo.duration,
        };
    },

    // MARK: - UPLOAD THUMBNAIL
    uploadThumbnail: async ({ videoId, file }) => {
        const existedVideo = await VideoModel.findById(videoId);

        if (!existedVideo) {
            throw VideoMessages.error.VIDEO_NOT_FOUND();
        }

        // Delete old thumbnail on Cloudinary if exists
        if (existedVideo.thumbnail?.cloudinaryId) {
            await cloudinaryUtil.deleteFile(existedVideo.thumbnail.cloudinaryId, "image");
        }

        const fileName = cloudinaryUtil.genFileName({
            prefix: "thumbnail",
            entityId: existedVideo._id,
        });

        const uploadedThumbnail = await cloudinaryUtil.uploadBuffer({
            fileBuffer: file.buffer,
            folder: UPLOAD_FOLDERS.VIDEOS.THUMBNAILS,
            fileName: fileName,
        });

        existedVideo.thumbnail = {
            url: uploadedThumbnail.secure_url,
            cloudinaryId: uploadedThumbnail.public_id,
        };

        await existedVideo.save();

        return {
            id: existedVideo._id,
            title: existedVideo.title,
            thumbnail: existedVideo.thumbnail,
        };
    },

    // MARK: - HELPER: PARSE TAGS
    _parseTags: (rawTags) => {
        if (!rawTags) return [];

        const tags = Array.isArray(rawTags) ? rawTags : [rawTags];
        try {
            // Check if tags[0] is a stringified array (common in multipart)
            if (tags.length > 0 && typeof tags[0] === "string" && tags[0].startsWith("[")) {
                return JSON.parse(tags[0]);
            }
            return tags;
        } catch (err) {
            return tags; // Fallback to raw tags
        }
    },

    // MARK: - CREATE VIDEO (metadata + upload video file in one step)
    createVideo: async ({ payload, file, userId }) => {
        const parsedTags = VideoService._parseTags(payload.tags);

        // Create the document first to get the _id for naming
        const newVideo = await VideoModel.create({
            title: payload.title,
            description: payload.description || "",
            categoryId: payload.categoryId,
            levelId: payload.levelId,
            tags: parsedTags,
            uploadedBy: userId,
            status: "draft",
        });

        // Upload the video file to Cloudinary using the new document _id
        const fileName = cloudinaryUtil.genFileName({
            prefix: "video",
            entityId: newVideo._id,
        });

        const uploadedVideo = await cloudinaryUtil.uploadVideo({
            fileBuffer: file.buffer,
            folder: UPLOAD_FOLDERS.VIDEOS.CLIPS,
            fileName,
        });

        // Save video URL + duration back to the document
        newVideo.video = {
            url: uploadedVideo.secure_url,
            cloudinaryId: uploadedVideo.public_id,
        };

        if (uploadedVideo.duration) {
            newVideo.duration = Math.round(uploadedVideo.duration);
        }

        await newVideo.save();

        return newVideo;
    },

    // MARK: - GET VIDEO BY ID
    getVideoById: async (videoId) => {
        const video = await VideoModel.findOne({ _id: videoId, isDeleted: false })
            .populate("categoryId", "name")
            .populate("levelId", "name")
            .populate("uploadedBy", "username avatar");

        if (!video) {
            throw VideoMessages.error.VIDEO_NOT_FOUND();
        }

        // Increment view count
        await VideoModel.findByIdAndUpdate(videoId, { $inc: { views: 1 } });

        return video;
    },

    // MARK: - GET ALL VIDEOS (with optional filters)
    getVideos: async ({ categoryId, levelId, status = "published", page = 1, limit = 10 } = {}) => {
        const filter = { isDeleted: false, status };

        if (categoryId) filter.categoryId = categoryId;
        if (levelId) filter.levelId = levelId;

        const skip = (page - 1) * limit;

        const [videos, total] = await Promise.all([
            VideoModel.find(filter)
                .populate("categoryId", "name")
                .populate("levelId", "name")
                .populate("uploadedBy", "username avatar")
                .sort({ createdAt: -1 })
                .skip(skip)
                .limit(limit),
            VideoModel.countDocuments(filter),
        ]);

        return {
            videos,
            pagination: {
                total,
                page,
                limit,
                totalPages: Math.ceil(total / limit),
            },
        };
    },

    // MARK: - SOFT DELETE VIDEO
    deleteVideo: async (videoId) => {
        const video = await VideoModel.findById(videoId);

        if (!video) {
            throw VideoMessages.error.VIDEO_NOT_FOUND();
        }

        video.isDeleted = true;
        await video.save();

        return { id: video._id };
    },

    // MARK: - UPDATE BASIC INFO (Admin only)
    updateVideoInfo: async ({ videoId, payload }) => {
        const video = await VideoModel.findById(videoId);
        if (!video) {
            throw VideoMessages.error.VIDEO_NOT_FOUND();
        }

        // Apply updates
        const updateFields = ["title", "description", "status", "tags", "categoryId", "levelId"];
        updateFields.forEach((field) => {
            if (payload[field] !== undefined) {
                if (field === "tags") {
                    video[field] = VideoService._parseTags(payload[field]);
                } else {
                    video[field] = payload[field];
                }
            }
        });

        await video.save();
        return video;
    },
};

export default VideoService;
