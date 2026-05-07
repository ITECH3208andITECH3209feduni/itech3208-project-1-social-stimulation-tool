import FeedbackModel from "#models/feedback.model.js";
import VideoModel from "#models/video.model.js";
import FeedbackMessages from "./feedback.message.js";

const FeedbackService = {
    // MARK: - HELPER: FORMAT FEEDBACK
    _formatFeedback: (feedback) => {
        if (!feedback) return null;

        const feedbackObj = feedback._doc || feedback;
        const { _id, __v, isDeleted, userId, videoId, ...rest } = feedbackObj;
        
        const formatted = { id: _id, ...rest };

        if (userId) {
            formatted.user = userId._id ? {
                id: userId._id,
                username: userId.username,
                avatar: userId.avatar,
            } : userId;
        }

        if (videoId) {
            formatted.video = videoId._id ? {
                id: videoId._id,
                title: videoId.title,
                thumbnail: videoId.thumbnail,
            } : videoId;
        }

        return formatted;
    },

    // MARK: - CREATE FEEDBACK
    createFeedback: async ({ userId, videoId, parentId, content, rating }) => {
        const normalizedParentId = parentId === "" ? null : parentId;

        const video = await VideoModel.findById(videoId);
        if (!video) {
            throw new Error("Video not found");
        }

        if (normalizedParentId) {
            const parent = await FeedbackModel.findById(normalizedParentId);
            if (!parent || parent.isDeleted) {
                throw FeedbackMessages.error.PARENT_NOT_FOUND();
            }
        }

        const newFeedback = await FeedbackModel.create({
            userId,
            videoId,
            parentId: normalizedParentId,
            content,
            rating,
        });

        const feedback = await FeedbackModel.findById(newFeedback._id)
            .populate("userId", "username avatar")
            .lean();

        return FeedbackService._formatFeedback(feedback);
    },

    // MARK: - GET FEEDBACKS BY VIDEO
    getFeedbacksByVideo: async (videoId, { page = 1, limit = 20 } = {}) => {
        const skip = (page - 1) * limit;

        const [feedbacks, total] = await Promise.all([
            FeedbackModel.find({ videoId, isDeleted: false, parentId: null })
                .populate("userId", "username avatar")
                .sort({ createdAt: -1 })
                .skip(skip)
                .limit(limit)
                .lean(),
            FeedbackModel.countDocuments({ videoId, isDeleted: false, parentId: null }),
        ]);

        const feedbacksWithReplies = await Promise.all(
            feedbacks.map(async (fb) => {
                const replies = await FeedbackModel.find({ parentId: fb._id, isDeleted: false })
                    .populate("userId", "username avatar")
                    .sort({ createdAt: 1 })
                    .lean();

                const formattedReplies = replies.map(FeedbackService._formatFeedback);
                const formattedFb = FeedbackService._formatFeedback(fb);

                return { ...formattedFb, replies: formattedReplies };
            })
        );

        return {
            feedbacks: feedbacksWithReplies,
            pagination: {
                total,
                page,
                limit,
                totalPages: Math.ceil(total / limit),
            },
        };
    },

    // MARK: - GET TOP FEEDBACKS (For Landing Page)
    getTopFeedbacks: async (limit = 6) => {
        const topVideos = await FeedbackModel.aggregate([
            { $match: { isDeleted: false, rating: { $gt: 0 } } },
            {
                $group: {
                    _id: "$videoId",
                    avgRating: { $avg: "$rating" },
                    count: { $sum: 1 },
                },
            },
            { $match: { count: { $gte: 1 } } },
            { $sort: { avgRating: -1, count: -1 } },
            { $limit: 10 },
        ]);

        const videoIds = topVideos.map((v) => v._id);

        const feedbacks = await FeedbackModel.find({
            videoId: { $in: videoIds },
            isDeleted: false,
            parentId: null,
            rating: { $gte: 4 },
            content: { $ne: "" },
        })
            .populate("userId", "username avatar")
            .populate("videoId", "title thumbnail")
            .sort({ rating: -1, createdAt: -1 })
            .limit(limit)
            .lean();

        return feedbacks.map(FeedbackService._formatFeedback);
    },

    // MARK: - DELETE FEEDBACK
    deleteFeedback: async (feedbackId, userId) => {
        const feedback = await FeedbackModel.findById(feedbackId);

        if (!feedback || feedback.isDeleted) {
            throw FeedbackMessages.error.FEEDBACK_NOT_FOUND();
        }

        if (feedback.userId.toString() !== userId.toString()) {
            throw new Error("Unauthorized to delete this feedback");
        }

        feedback.isDeleted = true;
        await feedback.save();

        return { id: feedback._id };
    },
};

export default FeedbackService;
