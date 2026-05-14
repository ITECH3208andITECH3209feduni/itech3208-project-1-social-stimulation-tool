import React from "react";
import { Grid, GridItem } from "@chakra-ui/react";
import VideoCard from "@/components/common/cards/VideoCard";

export const videoList = [
    {
        id: 1,
        title: "Nue Camp",
        description: "Explore the beauty of nature and outdoor camping تجربة.",
        thumbnail: "https://picsum.photos/800/450?random=1",
        duration: "09:04:05",
        comments: 49,
        views: 500,
    },
    {
        id: 2,
        title: "Mountain Hiking",
        description: "A full-day hiking journey through breathtaking mountain trails.",
        thumbnail: "https://picsum.photos/800/450?random=2",
        duration: "01:45:20",
        comments: 32,
        views: 1200,
    },
    {
        id: 3,
        title: "City Night Life",
        description: "Discover the vibrant nightlife in a modern city.",
        thumbnail: "https://picsum.photos/800/450?random=3",
        duration: "00:58:10",
        comments: 21,
        views: 860,
    },
    {
        id: 4,
        title: "Beach Relaxation",
        description: "Relax and unwind with calming ocean waves and sunset views.",
        thumbnail: "https://picsum.photos/800/450?random=4",
        duration: "02:10:45",
        comments: 67,
        views: 2300,
    },
    {
        id: 5,
        title: "Street Food Tour",
        description: "Taste amazing street food from different cultures.",
        thumbnail: "https://picsum.photos/800/450?random=5",
        duration: "00:35:12",
        comments: 15,
        views: 640,
    },
    {
        id: 6,
        title: "Tech Conference 2026",
        description: "Highlights from the biggest tech conference of the year.",
        thumbnail: "https://picsum.photos/800/450?random=6",
        duration: "03:22:18",
        comments: 88,
        views: 5400,
    },
    {
        id: 7,
        title: "Fitness Workout",
        description: "A complete home workout routine for beginners.",
        thumbnail: "https://picsum.photos/800/450?random=7",
        duration: "00:25:30",
        comments: 40,
        views: 1900,
    },
    {
        id: 8,
        title: "Cooking Masterclass",
        description: "Learn to cook delicious meals step by step.",
        thumbnail: "https://picsum.photos/800/450?random=8",
        duration: "01:15:00",
        comments: 52,
        views: 2100,
    },
    {
        id: 9,
        title: "Travel Vlog Japan",
        description: "Experience the culture and beauty of Japan سفر.",
        thumbnail: "https://picsum.photos/800/450?random=9",
        duration: "02:05:44",
        comments: 73,
        views: 3100,
    },
    {
        id: 10,
        title: "Gaming Highlights",
        description: "Top plays and funny moments from recent games.",
        thumbnail: "https://picsum.photos/800/450?random=10",
        duration: "00:42:55",
        comments: 28,
        views: 980,
    },
    {
        id: 11,
        title: "Travel Vlog Japan 2",
        description: "Experience the culture and beauty of Japan سفر.",
        thumbnail: "https://picsum.photos/800/450?random=11",
        duration: "02:05:44",
        comments: 73,
        views: 3100,
    },
    {
        id: 12,
        title: "Gaming Highlights 2",
        description: "Top plays and funny moments from recent games.",
        thumbnail: "https://picsum.photos/800/450?random=12",
        duration: "00:42:55",
        comments: 28,
        views: 980,
    },
];

function VideoGrid() {
    return (
        <Grid gap={4} templateColumns={"repeat(3, 1fr)"} templateRows={"repeat(4, 1fr)"}>
            {videoList.map((video) => (
                <GridItem key={video.id}>
                    <VideoCard video={video} />
                </GridItem>
            ))}
        </Grid>
    );
}

export default VideoGrid;
