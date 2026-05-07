import { WishlistModel } from "#models/index.js";
import WishlistMessages from "./wishlist.message.js";

const WishlistService = {
    // MARK: - TOGGLE WISHLIST
    toggleWishlist: async ({ userId, videoId }) => {
        const existedWishlist = await WishlistModel.findOne({ userId, videoId });

        if (existedWishlist) {
            await WishlistModel.findByIdAndDelete(existedWishlist._id);
            return { isAdded: false };
        }

        await WishlistModel.create({ userId, videoId });
        return { isAdded: true };
    },

    // MARK: - GET USER WISHLIST
    getWishlist: async (userId, { page = 1, limit = 10 } = {}) => {
        const skip = (page - 1) * limit;

        const [items, total] = await Promise.all([
            WishlistModel.find({ userId })
                .populate({
                    path: "videoId",
                    select: "title video thumbnail description duration views",
                    populate: [
                        { path: "categoryId", select: "name" },
                        { path: "levelId", select: "name" }
                    ]
                })
                .sort({ createdAt: -1 })
                .skip(skip)
                .limit(limit)
                .lean(),
            WishlistModel.countDocuments({ userId }),
        ]);

        // Clean up the response to only return video data
        const wishlist = items
            .filter(item => item.videoId) // Filter out if video was deleted
            .map(item => {
                const video = item.videoId;
                return {
                    id: item._id,
                    video: {
                        id: video._id,
                        ...video
                    },
                    addedAt: item.createdAt
                };
            });

        return {
            wishlist,
            pagination: {
                total,
                page,
                limit,
                totalPages: Math.ceil(total / limit),
            },
        };
    },
};

export default WishlistService;
