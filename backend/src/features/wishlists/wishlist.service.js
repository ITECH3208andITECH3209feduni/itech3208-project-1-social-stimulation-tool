import { WishlistModel } from "#models/index.js";
import WishlistMessages from "./wishlist.message.js";

const WishlistService = {
    _formatWishlist: (item) => {
        if (!item) return null;
        
        const formatted = {
            id: item._id,
            addedAt: item.createdAt,
        };

        if (item.videoId) {
            const v = item.videoId;
            formatted.video = {
                id: v._id,
                title: v.title,
                thumbnail: v.thumbnail,
                description: v.description,
                duration: v.duration,
                views: v.views,
                category: v.categoryId ? { id: v.categoryId._id, name: v.categoryId.name } : null,
                level: v.levelId ? { id: v.levelId._id, name: v.levelId.name } : null,
            };
        }

        return formatted;
    },

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

        return {
            wishlist: items
                .filter(item => item.videoId)
                .map(WishlistService._formatWishlist),
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
