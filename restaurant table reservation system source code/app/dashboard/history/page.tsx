import Link from "next/link"
import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import { CalendarPlus, MapPin, Users, Clock, Star } from "lucide-react"

// Mock data
const pastReservations = [
  {
    id: "RES-2026-0290",
    zone: "Main Dining Room",
    date: "Saturday, February 14, 2026",
    time: "7:00 PM",
    partySize: 2,
    rating: 5,
  },
  {
    id: "RES-2026-0285",
    zone: "The Patio",
    date: "Friday, February 7, 2026",
    time: "6:30 PM",
    partySize: 4,
    rating: 4,
  },
  {
    id: "RES-2026-0270",
    zone: "The Bar",
    date: "Saturday, January 25, 2026",
    time: "8:00 PM",
    partySize: 3,
    rating: 5,
  },
  {
    id: "RES-2025-0250",
    zone: "Main Dining Room",
    date: "Friday, December 20, 2025",
    time: "7:30 PM",
    partySize: 6,
    rating: 5,
  },
  {
    id: "RES-2025-0240",
    zone: "The Patio",
    date: "Saturday, November 15, 2025",
    time: "6:00 PM",
    partySize: 2,
    rating: 4,
  },
]

export default function HistoryPage() {
  return (
    <div className="p-6 lg:p-8 space-y-8">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl md:text-3xl text-foreground">Reservation History</h1>
          <p className="text-muted-foreground mt-1">
            View your past dining experiences at Eudaimonia
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
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        <Card>
          <CardContent className="p-4 text-center">
            <p className="text-2xl font-semibold text-foreground">{pastReservations.length}</p>
            <p className="text-sm text-muted-foreground">Total Visits</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4 text-center">
            <p className="text-2xl font-semibold text-foreground">
              {pastReservations.filter(r => r.zone === "Main Dining Room").length}
            </p>
            <p className="text-sm text-muted-foreground">Dining Room</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4 text-center">
            <p className="text-2xl font-semibold text-foreground">
              {pastReservations.filter(r => r.zone === "The Patio").length}
            </p>
            <p className="text-sm text-muted-foreground">Patio</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4 text-center">
            <p className="text-2xl font-semibold text-foreground">
              {pastReservations.filter(r => r.zone === "The Bar").length}
            </p>
            <p className="text-sm text-muted-foreground">Bar</p>
          </CardContent>
        </Card>
      </div>

      {/* History List */}
      <div className="space-y-4">
        {pastReservations.map((reservation) => (
          <Card key={reservation.id}>
            <CardContent className="p-6">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div className="space-y-3">
                  <div className="flex items-center gap-3">
                    <h3 className="font-serif text-lg text-foreground">{reservation.zone}</h3>
                    <div className="flex items-center gap-0.5">
                      {[...Array(5)].map((_, i) => (
                        <Star
                          key={i}
                          className={`h-4 w-4 ${
                            i < reservation.rating
                              ? "text-accent fill-accent"
                              : "text-muted-foreground"
                          }`}
                        />
                      ))}
                    </div>
                  </div>

                  <div className="flex flex-wrap gap-4 text-sm text-muted-foreground">
                    <div className="flex items-center gap-2">
                      <Clock className="h-4 w-4" />
                      <span>{reservation.date} at {reservation.time}</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <Users className="h-4 w-4" />
                      <span>{reservation.partySize} guests</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <MapPin className="h-4 w-4" />
                      <span>Eudaimonia</span>
                    </div>
                  </div>

                  <p className="text-xs text-muted-foreground">
                    Confirmation #{reservation.id}
                  </p>
                </div>

                <Button variant="outline" size="sm">
                  Book Again
                </Button>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {pastReservations.length === 0 && (
        <Card>
          <CardContent className="py-12 text-center">
            <Clock className="h-12 w-12 text-muted-foreground/50 mx-auto mb-4" />
            <h3 className="font-medium text-foreground mb-2">No past reservations</h3>
            <p className="text-sm text-muted-foreground mb-4">
              Your dining history will appear here
            </p>
            <Link href="/dashboard/book">
              <Button className="bg-primary text-primary-foreground">
                Make Your First Reservation
              </Button>
            </Link>
          </CardContent>
        </Card>
      )}
    </div>
  )
}
