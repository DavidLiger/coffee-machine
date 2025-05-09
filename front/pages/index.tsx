import { useEffect, useState } from "react";
import { Card, CardContent } from "@/components/ui/card";

interface CoffeeOrder {
  id: string;
  type: string;
  intensity: string;
  size: string;
  status: string;
  createdAt: string;
  startedAt?: string;
  endedAt?: string;
  stepsLog?: string[];
}

export default function CoffeeDashboard() {
  const [queued, setQueued] = useState<CoffeeOrder[]>([]);
  const [current, setCurrent] = useState<CoffeeOrder | null>(null);
  const [done, setDone] = useState<CoffeeOrder[]>([]);

  useEffect(() => {
    const ws = new WebSocket("ws://localhost:3001"); // à adapter à ton backend

    ws.onmessage = (event) => {
      const data = JSON.parse(event.data);
      const { event: eventType, order } = data;

      switch (eventType) {
        case "order.queued":
          setQueued((prev) => [...prev, order]);
          break;
        case "order.updated":
          setQueued((prev) => prev.filter((o) => o.id !== order.id));
          setCurrent(order);
          break;
        case "order.done":
          setCurrent(null);
          setDone((prev) => [order, ...prev]);
          break;
      }
    };

    return () => ws.close();
  }, []);

  return (
    <div className="p-4 grid gap-6">
      <section>
        <h2 className="text-xl font-bold mb-2 text-yellow-700">🟡 Commandes en attente</h2>
        <div className="max-h-60 overflow-y-auto grid gap-2">
          {queued.map((order) => (
            <Card key={order.id} className="bg-yellow-100">
              <CardContent>
                {order.type} - {order.size} - {order.intensity}
              </CardContent>
            </Card>
          ))}
        </div>
      </section>

      <section>
        <h2 className="text-xl font-bold mb-2 text-blue-700">🔵 Commande en cours</h2>
        {current ? (
          <Card className="bg-blue-100 animate-pulse">
            <CardContent>
              <p>{current.type} ({current.size}, {current.intensity})</p>
              <ul className="mt-2 text-sm">
                {current.stepsLog?.map((step, i) => (
                  <li key={i}>➡️ {step}</li>
                ))}
              </ul>
            </CardContent>
          </Card>
        ) : (
          <p>Aucune commande en cours</p>
        )}
      </section>

      <section>
        <h2 className="text-xl font-bold mb-2 text-green-700">🟢 Commandes terminées</h2>
        <div className="flex overflow-x-auto gap-2">
          {done.map((order) => (
            <Card key={order.id} className="bg-green-100 min-w-[200px]">
              <CardContent>
                <p>{order.type}</p>
                <p className="text-xs">Terminé à : {new Date(order.endedAt || '').toLocaleTimeString()}</p>
              </CardContent>
            </Card>
          ))}
        </div>
      </section>
    </div>
  );
}
