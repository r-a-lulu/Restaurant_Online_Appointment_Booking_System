import Link from "next/link"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { CalendarPlus, CalendarCheck, Clock, ArrowRight } from "lucide-react"

// Mock data - in production this would come from the database
const upcomingReservations = [
  {
    id: "RES-2026-0301",
    zone: "Main Dining Room",
    date: "Saturday, March 14, 2026",
    time: "7:30 PM",
    partySize: 4,
    status: "confirmed",
  },
  {
    id: "RES-2026-0302",
    zone: "The Bar",
    date: "Friday, March 20, 2026",
    time: "6:00 PM",
    partySize: 2,
    status: "pending",
  },
]

const stats = [
  { label: "Upcoming", value: "2", icon: CalendarCheck },
  { label: "Completed", value: "8", icon: Clock },
  { label: "This Month", value: "3", icon: CalendarPlus },
]

export default function DashboardPage() {
  return (
    <div className="p-6 lg:p-8 space-y-8">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl md:text-3xl text-foreground">Welcome back, John</h1>
          <p className="text-muted-foreground mt-1">
            Manage your reservations and dining preferences
          </p>
        </div>
        <Link href="/dashboard/book">
          <Button className="bg-primary text-primary-foreground hover:bg-primary/90">
            <CalendarPlus className="h-4 w-4 mr-2" />
            New Reservation
          </Button>
        </Link>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {stats.map((stat) => (
          <Card key={stat.label}>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                  <stat.icon className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <p className="text-2xl font-semibold text-foreground">{stat.value}</p>
                  <p className="text-sm text-muted-foreground">{stat.label}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Upcoming Reservations */}
      <Card>
        <CardHeader className="flex flex-row items-center justify-between">
          <CardTitle className="font-serif text-xl">Upcoming Reservations</CardTitle>
          <Link href="/dashboard/reservations">
            <Button variant="ghost" size="sm" className="text-primary">
              View All
              <ArrowRight className="h-4 w-4 ml-1" />
            </Button>
          </Link>
        </CardHeader>
        <CardContent>
          {upcomingReservations.length > 0 ? (
            <div className="space-y-4">
              {upcomingReservations.map((reservation) => (
                <div
                  key={reservation.id}
                  className="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-muted rounded-sm gap-4"
                >
                  <div className="space-y-1">
                    <div className="flex items-center gap-2">
                      <h3 className="font-medium text-foreground">{reservation.zone}</h3>
                      <span
                        className={`text-xs px-2 py-0.5 rounded-full ${
                          reservation.status === "confirmed"
                            ? "bg-green-100 text-green-700"
                            : "bg-yellow-100 text-yellow-700"
                        }`}
                      >
                        {reservation.status}
                      </span>
                    </div>
                    <p className="text-sm text-muted-foreground">
                      {reservation.date} at {reservation.time}
                    </p>
                    <p className="text-sm text-muted-foreground">
                      {reservation.partySize} guests
                    </p>
                  </div>
                  <div className="flex gap-2">
                    <Button variant="outline" size="sm">
                      Modify
                    </Button>
                    <Button variant="ghost" size="sm" className="text-destructive hover:text-destructive">
                      Cancel
                    </Button>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-12">
              <CalendarCheck className="h-12 w-12 text-muted-foreground/50 mx-auto mb-4" />
              <h3 className="font-medium text-foreground mb-2">No upcoming reservations</h3>
              <p className="text-sm text-muted-foreground mb-4">
                Book a table to start your dining experience
              </p>
              <Link href="/dashboard/book">
                <Button className="bg-primary text-primary-foreground">
                  Book Now
                </Button>
              </Link>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Quick Actions */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <Card className="hover:border-primary/50 transition-colors cursor-pointer">
          <Link href="/dining-zones">
            <CardContent className="p-6 flex items-center gap-4">
              <div className="w-12 h-12 rounded-full bg-accent flex items-center justify-center">
                <span className="font-serif text-lg text-foreground">E</span>
              </div>
              <div>
                <h3 className="font-medium text-foreground">Explore Dining Zones</h3>
                <p className="text-sm text-muted-foreground">
                  Discover our unique dining spaces
                </p>
              </div>
            </CardContent>
          </Link>
        </Card>

        <Card className="hover:border-primary/50 transition-colors cursor-pointer">
          <Link href="/dashboard/profile">
            <CardContent className="p-6 flex items-center gap-4">
              <div className="w-12 h-12 rounded-full bg-accent flex items-center justify-center">
                <span className="font-serif text-lg text-foreground">P</span>
              </div>
              <div>
                <h3 className="font-medium text-foreground">Update Preferences</h3>
                <p className="text-sm text-muted-foreground">
                  Manage your dining preferences
                </p>
              </div>
            </CardContent>
          </Link>
        </Card>
      </div>
    </div>
  )
}
