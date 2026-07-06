import { Grid, GridItem } from "@chakra-ui/react";
import UserSkeletonCard from "../cards/UserSkeletonCard";

function UserSkeletonGrid({ count = 6 }) {
    return (
        <Grid gap={4} templateColumns={{ base: "1fr", md: "repeat(2, 1fr)", lg: "repeat(3, 1fr)" }}>
            {Array.from({ length: count }).map((_, i) => (
                <GridItem key={i}>
                    <UserSkeletonCard />
                </GridItem>
            ))}
        </Grid>
    );
}

export default UserSkeletonGrid;
