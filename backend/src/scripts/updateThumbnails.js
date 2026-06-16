import { cloudinary } from "#config/cloudinary.config.js";
import mongoConfig from "#config/mongodb.config.js";
import VideoModel from "#models/video.model.js";

async function updateThumbnails() {
    try {
        console.log("Connecting to MongoDB...");
        await mongoConfig.connect();
        console.log("Connected successfully!");

        // Fetch all videos
        const videos = await VideoModel.find({});
        console.log(`Found ${videos.length} videos to update.`);

        let updatedCount = 0;
        let skipCount = 0;
        let errorCount = 0;

        for (const video of videos) {
            try {
                // If the video doesn't have a valid cloudinaryId, skip it
                if (!video.video || !video.video.cloudinaryId) {
                    console.log(`[SKIP] Video ${video._id} does not have a cloudinaryId.`);
                    skipCount++;
                    continue;
                }

                const publicId = video.video.cloudinaryId;

                // Method 1: Generate thumbnail URL locally (Fastest, no rate limits)
                // Cloudinary generates thumbnails for videos by changing the format to 'jpg'
                const thumbnailUrl = cloudinary.url(publicId, {
                    resource_type: "video",
                    format: "jpg",
                    secure: true,
                });

                // Alternatively, if you explicitly want to call Cloudinary API to verify it exists:
                // const result = await cloudinary.api.resource(publicId, { resource_type: "video" });
                // const thumbnailUrl = result.secure_url.replace(/\.(mp4|webm|ogg)$/i, ".jpg");

                // Update the thumbnail field
                video.thumbnail = {
                    url: thumbnailUrl,
                    cloudinaryId: publicId, // The cloudinary ID for the thumbnail is the same as the video
                };

                await video.save();
                console.log(`[SUCCESS] Updated thumbnail for video ${video._id}`);
                updatedCount++;
            } catch (err) {
                console.error(`[ERROR] Failed to update video ${video._id}:`, err.message);
                errorCount++;
            }
        }

        console.log("--------------------------------------------------");
        console.log(`Migration completed!`);
        console.log(`Total Videos: ${videos.length}`);
        console.log(`Updated: ${updatedCount}`);
        console.log(`Skipped: ${skipCount}`);
        console.log(`Errors: ${errorCount}`);
        console.log("--------------------------------------------------");
    } catch (error) {
        console.error("Migration failed:", error);
    } finally {
        await mongoConfig.disconnect();
        process.exit(0);
    }
}

// updateThumbnails();
