import UPLOAD_FOLDERS from "#constants/upload-folder.constant.js";
import { VideoModel } from "#models/index.js";
import { cloudinaryUtil } from "#utils/index.js";
import VideoMessages from "./video.message.js";

const VideoService = {
    _formatVideo: (video) => {
        if (!video) return null;
        const videoObj = video._doc || video;
        const { _id, __v, isDeleted, uploadedBy, categoryId, levelId, ...rest } = videoObj;

        const formatted = {
            id: _id,
            ...rest,
        };

        if (uploadedBy) {
            formatted.user = uploadedBy._id ? {
                id: uploadedBy._id,
                username: uploadedBy.username,
                avatar: uploadedBy.avatar,
            } : uploadedBy;
        }

        if (categoryId) {
            formatted.category = categoryId._id ? {
                id: categoryId._id,
                name: categoryId.name,
            } : categoryId;
        }

        if (levelId) {
            formatted.level = levelId._id ? {
                id: levelId._id,
                name: levelId.name,
            } : levelId;
        }

        return formatted;
    },

    // MARK: - UPLOAD VIDEO FILE
    uploadVideo: async ({ videoId, file }) => {
        const existedVideo = await VideoModel.findById(videoId);

        if (!existedVideo) {
            throw VideoMessages.error.VIDEO_NOT_FOUND();
        }

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

        if (uploadedVideo.duration) {
            existedVideo.duration = Math.round(uploadedVideo.duration);
        }

        await existedVideo.save();

        return VideoService._formatVideo(existedVideo);
    },

    // MARK: - UPLOAD THUMBNAIL
    uploadThumbnail: async ({ videoId, file }) => {
        const existedVideo = await VideoModel.findById(videoId);

        if (!existedVideo) {
            throw VideoMessages.error.VIDEO_NOT_FOUND();
        }

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

        return VideoService._formatVideo(existedVideo);
    },

    // MARK: - HELPER: PARSE TAGS
    _parseTags: (rawTags) => {
        if (!rawTags) return [];

        const tags = Array.isArray(rawTags) ? rawTags : [rawTags];
        try {
            if (tags.length > 0 && typeof tags[0] === "string" && tags[0].startsWith("[")) {
                return JSON.parse(tags[0]);
            }
            return tags;
        } catch (err) {
            return tags;
        }
    },

    // MARK: - CREATE VIDEO (metadata + upload video file in one step)
    createVideo: async ({ payload, file, userId }) => {
        const parsedTags = VideoService._parseTags(payload.tags);

        const newVideo = await VideoModel.create({
            title: payload.title,
            description: payload.description || "",
            categoryId: payload.categoryId,
            levelId: payload.levelId,
            tags: parsedTags,
            uploadedBy: userId,
            status: "draft",
        });

        const fileName = cloudinaryUtil.genFileName({
            prefix: "video",
            entityId: newVideo._id,
        });

        const uploadedVideo = await cloudinaryUtil.uploadVideo({
            fileBuffer: file.buffer,
            folder: UPLOAD_FOLDERS.VIDEOS.CLIPS,
            fileName,
        });

        newVideo.video = {
            url: uploadedVideo.secure_url,
            cloudinaryId: uploadedVideo.public_id,
        };

        if (uploadedVideo.duration) {
            newVideo.duration = Math.round(uploadedVideo.duration);
        }

        await newVideo.save();

        return VideoService._formatVideo(newVideo);
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

        await VideoModel.findByIdAndUpdate(videoId, { $inc: { views: 1 } });

        return VideoService._formatVideo(video);
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
            videos: videos.map(VideoService._formatVideo),
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

    // MARK: - UPDATE BASIC INFO
    updateVideoInfo: async ({ videoId, payload }) => {
        const video = await VideoModel.findById(videoId);
        if (!video) {
            throw VideoMessages.error.VIDEO_NOT_FOUND();
        }

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
        return VideoService._formatVideo(video);
    },
};

export default VideoService;
