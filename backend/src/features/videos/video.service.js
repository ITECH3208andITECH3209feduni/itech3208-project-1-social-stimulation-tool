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
            await cloudinaryUtil.deleteFile(existedVideo.video.cloudinaryId);
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
            await cloudinaryUtil.deleteFile(existedVideo.thumbnail.cloudinaryId);
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

    // MARK: - CREATE VIDEO (metadata + upload video file in one step)
    createVideo: async ({ payload, file, userId }) => {
        // Normalize tags: multipart may send single tag as string instead of array
        const tags = Array.isArray(payload.tags)
            ? payload.tags
            : payload.tags
              ? [payload.tags]
              : [];

        let parsedTags = [];

        try {
            // Check if tags[0] exists and is a string before parsing
            if (tags.length > 0 && typeof tags[0] === "string" && tags[0].startsWith("[")) {
                parsedTags = JSON.parse(tags[0]);
            } else {
                parsedTags = tags;
            }
        } catch (err) {
            parsedTags = tags; // Fallback to raw tags if parse fails
        }

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
};

export default VideoService;
